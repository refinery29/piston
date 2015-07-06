<?php namespace Refinery29\Piston\Router\Routes;

use Refinery29\Piston\Hooks\Hookable;

/**
 * Class RouteGroup
 * @package Refinery29\Piston\Router\Routes
 */
class RouteGroup
{
    /**
     * @var array
     */
    private $routes = [];

    use Hookable;

    /**
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param Route $route
     * @return bool
     */
    public function includes(Route $route)
    {
        foreach ($this->routes as $match_route) {
            if ($route->getAlias() == $match_route->getAlias()) {
                return true;
            }
        }

        return false;
    }
}
