<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/api")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("", name="api_home")
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(): JsonResponse
    {
        return new JsonResponse(['page' => 'home']);
    }
}