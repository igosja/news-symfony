<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Language;
use App\Form\LanguageForm;
use App\Repository\LanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/admin/{_locale}/language")
 */
class LanguageController extends AbstractController
{
    /**
     * @Route("", name="admin_language")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, LanguageRepository $languageRepository, TranslatorInterface $translator): Response
    {
        $filterUrl = $this->getFilterUrl('admin_language', $request);
        $languages = $languageRepository->findByFilters(
            $request->query->all(),
            $this->getSortingCriteria($request),
            $this->itemsPerPage,
            $this->getOffset($request)
        );
        $totalCount = count($languageRepository->findByFilters($request->query->all(), $this->getSortingCriteria($request)));
        $pages = $this->getPaginationPages($request, $totalCount);
        $paginationUrl = $this->getPaginationUrl('admin_language', $request);

        return $this->render('admin/language/index.html.twig', [
            'filter_url' => $filterUrl,
            'languages' => $languages,
            'pages' => $pages,
            'pagination_url' => $paginationUrl,
            'total_count' => $totalCount,
        ]);
    }

    /**
     * @Route("/view/{id}", name="admin_language_view")
     *
     * @param \App\Entity\Language $language
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(Language $language): Response
    {
        return $this->render('admin/language/view.html.twig', [
            'language' => $language,
        ]);
    }

    /**
     * @Route("/create", name="admin_language_create")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create(Request $request, LanguageRepository $languageRepository): Response
    {
        $language = new Language();
        $language->setCreatedAt(time());
        $language->setUpdatedAt(time());
        $form = $this->createForm(LanguageForm::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $languageRepository->add($language, true);

            return $this->redirectToRoute('admin_language', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/language/create.html.twig', [
            'language' => $language,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/update/{id}", name="admin_language_update")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Language $language
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Request $request, Language $language, LanguageRepository $languageRepository): Response
    {
        $language->setUpdatedAt(time());
        $form = $this->createForm(LanguageForm::class, $language);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $languageRepository->add($language, true);

            return $this->redirectToRoute('admin_language', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/language/update.html.twig', [
            'language' => $language,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="admin_language_delete")
     *
     * @param \App\Entity\Language $language
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Language $language, LanguageRepository $languageRepository): Response
    {
        $languageRepository->remove($language, true);

        return $this->redirectToRoute('admin_language', [], Response::HTTP_SEE_OTHER);
    }
}