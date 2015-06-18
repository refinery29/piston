<?php

namespace Refinery29\Piston\Routes;

use Refinery29\Piston\Hooks\Hookable;

class RouteGroup
{
    protected $routes = [];
    protected $groups = [];

    use Hookable;

    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
    }

    public function addGroup(RouteGroup $group)
    {
        $this->groups[] = $group;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getGroups()
    {
        return $this->groups;
    }
}
