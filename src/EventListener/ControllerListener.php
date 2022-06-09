<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Repository\LanguageRepository;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

/**
 * Class ControllerListener
 * @package App\EventListener
 */
class ControllerListener
{
    /**
     * @var \App\Repository\LanguageRepository $languageRepository
     */
    private LanguageRepository $languageRepository;

    /**
     * @var \Twig\Environment $twig
     */
    private Environment $twig;

    /**
     * @param \App\Repository\LanguageRepository $languageRepository
     * @param \Twig\Environment $twig
     */
    public function __construct(LanguageRepository $languageRepository, Environment $twig)
    {
        $this->languageRepository = $languageRepository;
        $this->twig = $twig;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\ControllerEvent $event
     * @return void
     */
    public function onKernelController(ControllerEvent $event): void
    {
        $languages = $this->languageRepository->findBy(['is_active' => true], ['id' => 'ASC']);
        $twigLanguages = [];
        foreach ($languages as $language) {
            $twigLanguages[] = $language->getCode();
        }

        $this->twig->addGlobal('db_languages', $twigLanguages);
    }
}