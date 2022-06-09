<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Repository\LanguageRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RequestListener
 * @package App\EventListener
 */
class RequestListener
{
    /**
     * @var \App\Entity\Language[] $languages
     */
    private array $languages;

    /**
     * @var \App\Repository\LanguageRepository $languageRepository
     */
    private LanguageRepository $languageRepository;

    /**
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router
     */
    private UrlGeneratorInterface $router;

    /**
     * @param \App\Repository\LanguageRepository $languageRepository
     * @param \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router
     */
    public function __construct(LanguageRepository $languageRepository, UrlGeneratorInterface $router)
    {
        $this->languageRepository = $languageRepository;
        $this->router = $router;
        $this->loadLanguages();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $currentLocale = $request->getLocale();

        if ($request->get('language')) {
            foreach ($this->languages as $language) {
                if ($language->getCode() === $request->get('language')) {
                    $session->set('language', $request->get('language'));
                }
            }
        }

        if ($session->get('language')) {
            $request->setLocale($session->get('language'));
        }

        $systemLanguage = null;
        foreach ($this->languages as $language) {
            if ($language->getCode() === $request->getLocale()) {
                $systemLanguage = $language->getCode();
            }
        }

        if (!$systemLanguage) {
            $systemLanguage = 'uk';
        }

        if ($systemLanguage !== $currentLocale) {
            $url = $this->router->generate($request->get('_route'), array_merge(
                $request->get('_route_params'),
                ['_locale' => $systemLanguage]
            ));

            $event->setResponse(new RedirectResponse($url, 302));
        }
    }

    /**
     * @return void
     */
    private function loadLanguages(): void
    {
        $this->languages = $this->languageRepository->findBy(['is_active' => true], ['id' => 'ASC']);
    }
}