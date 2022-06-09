<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryForm;
use App\Repository\CategoryRepository;
use App\Repository\LanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/admin/{_locale}/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("", name="admin_category")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, CategoryRepository $categoryRepository, TranslatorInterface $translator): Response
    {
        $filterUrl = $this->getFilterUrl('admin_category', $request);
        $categories = $categoryRepository->findByFilters(
            $request->query->all(),
            $this->getSortingCriteria($request),
            $this->itemsPerPage,
            $this->getOffset($request)
        );
        $totalCount = count($categoryRepository->findByFilters($request->query->all(), $this->getSortingCriteria($request)));
        $pages = $this->getPaginationPages($request, $totalCount);
        $paginationUrl = $this->getPaginationUrl('admin_category', $request);

        return $this->render('admin/category/index.html.twig', [
            'filter_url' => $filterUrl,
            'categories' => $categories,
            'pages' => $pages,
            'pagination_url' => $paginationUrl,
            'total_count' => $totalCount,
        ]);
    }

    /**
     * @Route("/view/{id}", name="admin_category_view")
     *
     * @param \App\Entity\Category $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Category $category, LanguageRepository $languageRepository): Response
    {
        return $this->render('admin/category/view.html.twig', [
            'category' => $category,
            'translation_languages' => $languageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="admin_category_create")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $category->setCreatedAt(time());
        $category->setUpdatedAt(time());
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('admin_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/create.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_category_update")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Category $category
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $category->setUpdatedAt(time());
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->add($category, true);

            return $this->redirectToRoute('admin_category', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/update.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_category_delete")
     *
     * @param \App\Entity\Category $category
     * @param \App\Repository\CategoryRepository $categoryRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Category $category, CategoryRepository $categoryRepository): Response
    {
        $categoryRepository->remove($category, true);

        return $this->redirectToRoute('admin_category', [], Response::HTTP_SEE_OTHER);
    }
}