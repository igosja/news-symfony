<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/admin")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("", name="admin_home_no_locale")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexNoLocale(Request $request): Response
    {
        return $this->redirect($this->generateUrl('admin_home', ['_locale' => $request->getLocale()]));
    }

    /**
     * @Route("/{_locale}", name="admin_home")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(): Response
    {
        return $this->render('admin/site/index.html.twig');
    }
}