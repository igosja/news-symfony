<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Rating;
use App\Form\CommentForm;
use App\Helper\ImageHelper;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\LanguageRepository;
use App\Repository\PostRepository;
use App\Repository\RatingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 *
 * @Route("/{_locale}/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("", name="post")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @param \App\Helper\ImageHelper $imageHelper
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository, ImageHelper $imageHelper): Response
    {
        $criteria = [
            'is_active' => true,
        ];
        if ($request->get('category_id')) {
            $criteria['category'] = $request->get('category_id');
        }
        $posts = $postRepository->findBy(
            $criteria,
            ['updated_at' => 'DESC'],
            10,
            ($request->get('page', 1) - 1) * 10
        );
        $categories = $categoryRepository->findBy(['is_active' => true]);

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
            'categories' => $categories,
            'image_helper' => $imageHelper,
        ]);
    }

    /**
     * @Route("/{url}", name="post_view")
     *
     * @param \App\Entity\Post $post
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Repository\CommentRepository $commentRepository
     * @param \App\Repository\LanguageRepository $languageRepository
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Helper\ImageHelper $imageHelper
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Post $post, PostRepository $postRepository, CommentRepository $commentRepository, LanguageRepository $languageRepository, Request $request, ImageHelper $imageHelper): Response
    {
        $post->setViews($post->getViews() + 1);
        $postRepository->add($post, true);

        $language = $languageRepository->findOneBy(['code' => $request->getLocale()]);
        $comments = $commentRepository->findBy(['post' => $post, 'language' => $language]);

        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $comment = new Comment();
        $comment->setCreatedAt(time());
        $comment->setCreatedBy($user);
        $comment->setUpdatedAt(time());
        $comment->setUpdatedBy($user);
        $comment->setLanguage($language);
        $comment->setPost($post);

        $form = $this->createForm(CommentForm::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->add($comment, true);

            return $this->redirectToRoute('post_view', ['url' => $post->getUrl()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/view.html.twig', [
            'comments' => $comments,
            'form' => $form->createView(),
            'post' => $post,
            'image_helper' => $imageHelper,
        ]);
    }

    /**
     * @Route("/rating/{url}", name="post_rating")
     *
     * @param \App\Entity\Post $post
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\RatingRepository $ratingRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function rating(Post $post, Request $request, RatingRepository $ratingRepository): Response
    {
        $value = (int)$request->get('value');
        if (!in_array($value, [1, -1], true)) {
            return $this->redirectToRoute('post_view', ['url' => $post->getUrl()]);
        }

        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();
        $rating = $ratingRepository->findOneBy(['post' => $post, 'created_by' => $user]);
        if (!$rating) {
            $rating = new Rating();
            $rating->setCreatedBy($user);
            $rating->setCreatedAt(time());
            $rating->setPost($post);
        }
        $rating->setUpdatedBy($user);
        $rating->setUpdatedAt(time());
        $rating->setValue($value);
        $ratingRepository->add($rating, true);

        return $this->redirectToRoute('post_view', ['url' => $post->getUrl()]);
    }
}