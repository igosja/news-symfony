<?php
declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class RequestListener
 * @package App\EventListener
 */
class RequestListener
{
    private UrlGeneratorInterface $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $language = $request->get('language');

        if ($language) {
            $request->setLocale($language);
            $url = $this->router->generate($request->get('_route'), array_merge(
                $request->get('_route_params'),
                ['_locale' => $language]
            ));

            $event->setResponse(new RedirectResponse($url, 302));
        }
    }
}