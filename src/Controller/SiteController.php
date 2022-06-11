<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SiteController
 * @package App\Controller
 */
class SiteController extends AbstractController
{
    /**
     * @Route("", name="home_no_locale")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexNoLocale(Request $request): Response
    {
        return $this->redirectToRoute('home', ['_locale' => $request->getLocale()]);
    }

    /**
     * @Route("/{_locale}", name="home")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function index(): Response
    {
        return $this->redirectToRoute('post');
    }

    /**
     * @Route("/{_locale}/login", name="login")
     *
     * @param \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('site/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/{_locale}/logout", name="logout")
     */
    public function logout(): Response
    {
        throw new RuntimeException('Will be intercepted before getting here');
    }

    /**
     * @Route("/signup", name="signup")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
     * @param \Doctrine\Persistence\ManagerRegistry $doctrine
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signup(Request $request, UserPasswordHasherInterface $passwordHasher, ManagerRegistry $doctrine): Response
    {
        if ($request->isMethod(Request::METHOD_POST) && $request->request->get('_email') && $request->request->get('_username') && $request->request->get('_password')) {
            $user = new User();

            $user->setEmail($request->request->get('_email'));
            $user->setUsername($request->request->get('_username'));
            $user->setRoles(['ROLE_USER']);

            $plaintextPassword = $request->request->get('_password');

            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('site/signup.html.twig', [
        ]);
    }
}