<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostForm;
use App\Repository\ImageRepository;
use App\Repository\LanguageRepository;
use App\Repository\PostRepository;
use App\Service\UploadImageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Class PostController
 * @package App\Admin\Controller
 *
 * @Route("/admin/{_locale}/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("", name="admin_post")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $filterUrl = $this->getFilterUrl('admin_post', $request);
        $posts = $postRepository->findByFilters(
            $request->query->all(),
            $this->getSortingCriteria($request),
            $this->itemsPerPage,
            $this->getOffset($request)
        );
        $totalCount = count($postRepository->findByFilters($request->query->all(), $this->getSortingCriteria($request)));
        $pages = $this->getPaginationPages($request, $totalCount);
        $paginationUrl = $this->getPaginationUrl('admin_post', $request);

        return $this->render('admin/post/index.html.twig', [
            'filter_url' => $filterUrl,
            'posts' => $posts,
            'pages' => $pages,
            'pagination_url' => $paginationUrl,
            'total_count' => $totalCount,
        ]);
    }

    /**
     * @Route("/view/{id}", name="admin_post_view")
     *
     * @param \App\Entity\Post $post
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Post $post, LanguageRepository $languageRepository): Response
    {
        return $this->render('admin/post/view.html.twig', [
            'post' => $post,
            'translation_languages' => $languageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="admin_post_create")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Service\UploadImageService $uploader
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, PostRepository $postRepository, UploadImageService $uploader, SluggerInterface $slugger): Response
    {
        /**
         * @var \App\Entity\User $user
         */
        $user = $this->getUser();

        $post = new Post();
        $post->setCreatedAt(time());
        $post->setCreatedBy($user);
        $post->setUpdatedAt(time());
        $post->setUpdatedBy($user);
        $post->setViews(0);

        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->add($post, true);
            $post->setUrl($post->getId() . '-' . $slugger->slug($post->getName()));
            $postRepository->add($post, true);

            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
             */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $image = $uploader->upload($imageFile, $this->getParameter('upload_directory'));
                $post->setImage($image);
                $postRepository->add($post, true);
            }

            return $this->redirectToRoute('admin_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/post/create.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_post_update")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Post $post
     * @param \App\Repository\PostRepository $postRepository
     * @param \App\Service\UploadImageService $uploader
     * @param \Symfony\Component\String\Slugger\SluggerInterface $slugger
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, Post $post, PostRepository $postRepository, UploadImageService $uploader, SluggerInterface $slugger): Response
    {
        $post->setUpdatedAt(time());
        $form = $this->createForm(PostForm::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->add($post, true);
            if (!$post->getUrl()) {
                $post->setUrl($post->getId() . '-' . $slugger->slug($post->getName()));
                $postRepository->add($post, true);
            }

            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
             */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $image = $uploader->upload($imageFile, $this->getParameter('upload_directory'));
                $post->setImage($image);
                $postRepository->add($post, true);
            }

            return $this->redirectToRoute('admin_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/post/update.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_post_delete")
     *
     * @param \App\Entity\Post $post
     * @param \App\Repository\PostRepository $postRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Post $post, PostRepository $postRepository): Response
    {
        $postRepository->remove($post, true);

        return $this->redirectToRoute('admin_post', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/delete-image/{id}", name="admin_post_delete_image")
     *
     * @param \App\Entity\Post $post
     * @param \App\Repository\ImageRepository $imageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteImage(Post $post, ImageRepository $imageRepository): Response
    {
        $image = $post->getImage();
        if ($image) {
            $post->setImage(null);
            $imageRepository->remove($image, true);
        }

        return $this->redirectToRoute('admin_post_update', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
    }
}