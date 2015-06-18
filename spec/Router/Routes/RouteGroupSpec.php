<?php

namespace spec\Refinery29\Piston\Router\Routes;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Refinery29\Piston\Router\Routes\Route;
use Refinery29\Piston\Router\Routes\RouteGroup;

class RouteGroupSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Router\Routes\RouteGroup');
    }

    public function it_can_add_a_route(Route $route)
    {
        $route->beADoubleOf('Refinery29\Piston\Router\Routes\Route');
        $this->addRoute($route);

        $this->getRoutes()->shouldContain($route);
    }

    public function it_can_add_a_group(RouteGroup $group)
    {
        $group->beADoubleOf('Refinery29\Piston\Router\Routes\RouteGroup');
        $this->addGroup($group);

        $this->getGroups()->shouldContain($group);
    }

    public function it_cannot_add_invalid_pre_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('addPreHook', [new \stdClass()]);
    }

    public function it_cannot_add_invalid_post_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('addPostHook', [new \stdClass()]);
    }

    public function it_can_add_pre_hooks()
    {
        $closure = function ($request, $response) {
            return $response;
        };

        $this->addPreHook($closure);

        $pre_hooks = $this->getPreHooks();

        $pre_hooks->shouldHaveType('Refinery29\Piston\Hooks\Queue');

        $pre_hooks->getNext()->shouldReturn($closure);
    }

    public function it_can_add_post_hooks()
    {
        $closure = function ($request, $response) {
            return $response;
        };

        $this->addPostHook($closure);

        $pre_hooks = $this->getPostHooks();

        $pre_hooks->shouldHaveType('Refinery29\Piston\Hooks\Queue');

        $pre_hooks->getNext()->shouldReturn($closure);
    }

    public function it_can_get_pre_hooks()
    {
        $this->getPreHooks()->shouldHaveType('Refinery29\Piston\Hooks\Queue');
    }

    public function it_can_get_post_hooks()
    {
        $this->getPostHooks()->shouldHaveType('Refinery29\Piston\Hooks\Queue');
    }
}
