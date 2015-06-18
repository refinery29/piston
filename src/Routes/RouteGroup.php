<?php

namespace Refinery29\Piston\Routes;

use Refinery29\Piston\Hooks\Hookable;

class RouteGroup
{
    use Hookable;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var array
     */
    protected $groups = [];

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