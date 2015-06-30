<?php

namespace spec\Refinery29\Piston\Router;

use League\Container\ContainerInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Refinery29\Piston\Router\Routes\Route;
use Refinery29\Piston\Router\Routes\RouteGroup;

class RouteCollectionSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Router\RouteCollection');
    }

    public function it_can_add_route_groups(RouteGroup $group, Route $route)
    {
        $group->getRoutes()->willReturn([$route]);
        $this->addGroup($group);

        $this->getGroups()->shouldContain($group);
    }

    public function it_can_find_route_by_action(Route $route)
    {
        $route->getAction()->willReturn('FooController::test');
        $route->getVerb()->willReturn('GET');
        $route->getAlias()->willReturn('123/345');
        $this->add($route);

        $this->findByAction(['FooController', 'test'])->shouldReturn($route);
    }

    public function it_cannot_find_a_nonexistant_route()
    {
        $this->findByAction(['FooController', 'test'])->shouldReturn(false);
    }

    public function it_can_find_group_by_route(Route $route, RouteGroup $group)
    {
        $route->getAction()->willReturn('FooController::test');
        $route->getVerb()->willReturn('GET');
        $route->getAlias()->willReturn('123/345');

        $group->getRoutes()->willReturn([$route]);
        $group->includes($route)->willReturn(true);

        $group->addRoute($route);

        $this->addGroup($group);

        $this->findGroupByRoute($route)->shouldReturn($group);
    }

    public function it_cannot_find_group(Route $route)
    {
        $route->getAction()->willReturn('FooController::test');
        $route->getVerb()->willReturn('GET');
        $route->getAlias()->willReturn('123/345');
        $this->add($route);

        $this->findGroupByRoute($route)->shouldReturn(false);
    }
}
