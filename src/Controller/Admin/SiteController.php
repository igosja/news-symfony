<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SiteController
 * @package App\Admin\Controller
 *
 * @Route("/admin")
 */
class SiteController extends AbstractController
{
    /**
     * @Route("", name="admin_home")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(): Response
    {
        return $this->render('admin/site/index.html.twig');
    }
}