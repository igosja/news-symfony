<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LanguageController
 * @package App\Admin\Controller
 *
 * @Route("/api/language")
 */
class LanguageController extends AbstractController
{
    /**
     * @Route("", name="api_language", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \App\Repository\LanguageRepository $languageRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, LanguageRepository $languageRepository): Response
    {
        $languages = $languageRepository->findBy(
            [],
            ['id' => 'DESC'],
            10,
            ($request->get('page', 1) - 1) * 10
        );
        return $this->jsonResult($languages);
    }

    /**
     * @Route("/view/{id}", name="api_language_view")
     *
     * @param \App\Entity\Language $language
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function view(Language $language): Response
    {
        return $this->jsonResult($language);
    }
}