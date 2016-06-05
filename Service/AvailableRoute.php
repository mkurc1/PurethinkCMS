<?php

namespace Purethink\CMSBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AvailableRoute
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getRoutes()
    {
        $availableRoutes = [];
        foreach ($this->router->getRouteCollection()->all() as $name => $route) {
            $availableRoutes[$name] = $name;
        }

        return $availableRoutes;
    }
}