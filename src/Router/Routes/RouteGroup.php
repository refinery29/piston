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
    protected $routes = [];
    /**
     * @var array
     */
    protected $groups = [];

    use Hookable;

    /**
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    /**
     * @param RouteGroup $group
     */
    public function addGroup(RouteGroup $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
