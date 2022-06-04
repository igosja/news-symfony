<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Language;
use App\Form\LanguageForm;
use App\Repository\LanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/admin/language")
 */
class LanguageController extends AbstractController
{
    /**
     * @Route("", name="admin_language")
     *
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(LanguageRepository $languageRepository): Response
    {
        return $this->render('admin/language/index.html.twig', [
            'languages' => $languageRepository->findAll(),
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Entity\Language $language
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Request $request, Language $language, LanguageRepository $languageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $language->getId(), $request->request->get('_token'))) {
            $languageRepository->remove($language, true);
        }

        return $this->redirectToRoute('admin_language', [], Response::HTTP_SEE_OTHER);
    }
}