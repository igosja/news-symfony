<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 *
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(): Response
    {
        return new Response('Post list');
    }

    /**
     * @Route("/{url}")
     *
     * @param string $url
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(string $url): Response
    {
        return new Response('Post item ' . $url);
    }

    /**
     * @Route("/rating")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function rating(): Response
    {
        return new Response('Post rating');
    }
}