<?php namespace Refinery29\Piston\Router;

use FastRoute\RouteParser\Std;
use League\Container\ContainerInterface;
use League\Route\RouteCollection as Router;
use Refinery29\Piston\Router\Routes\Route;
use Refinery29\Piston\Router\Routes\RouteGroup;

class RouteCollection extends Router
{
    /**
     * @var Route[]
     */
    private $route_objects = [];

    /**
     * @var RouteGroup[]
     */
    private $groups = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->parser = new Std();
    }

    /**
     * @param Route $route
     */
    public function add(Route $route)
    {
        parent::addRoute($route->getVerb(), $route->getAlias(), $route->getAction());
        $this->route_objects[] = $route;
    }

    /**
     * @param RouteGroup $group
     */
    public function addGroup(RouteGroup $group)
    {
        foreach ($group->getRoutes() as $route) {
            $this->add($route);
        }
        $this->groups[] = $group;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $action
     * @return Route
     */
    public function findByAction(array $action)
    {
        foreach ($this->route_objects as $route) {
            if ($route->getAction() == implode('::', $action)) {
                return $route;
            }
        }

        return false;
    }

    /**
     * @param Route $route
     * @return RouteGroup
     */
    public function findGroupByRoute(Route $route)
    {
        foreach ($this->groups as $group) {
            if ($group->includes($route)) {
                return $group;
            }
        }

        return false;
    }
}
