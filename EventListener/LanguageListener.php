<?php

namespace Purethink\CMSBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;

class LanguageListener
{
    const ONE_YEAR = 31536000;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setLocale(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if ($locale = $request->attributes->get('_locale')) {
            if ($this->getLanguage()->hasAvailableLocales($locale)) {
                $redirectResponse = new RedirectResponse($this->getHomepageRoute());

                $cookie = new Cookie('_locale', $locale, time() + self::ONE_YEAR);
                $redirectResponse->headers->setCookie($cookie);

                $event->setResponse($redirectResponse);
            }
        } else {
            $defaultLocale = $request->getPreferredLanguage($this->getLanguage()->getAvailableLocales());

            if (!$this->isAdminUrl($request->getRequestUri())) {
                $request->setLocale($request->cookies->get('_locale', $defaultLocale));
            }
        }
    }

    private function getHomepageRoute()
    {
        return $this->container->get('router')->generate('purethink_cms_homepage');
    }

    private function getLanguage()
    {
        return $this->container->get('app.language_service');
    }

    private function isAdminUrl($uri)
    {
        return preg_match('/admin\/purethink/', $uri);
    }
}