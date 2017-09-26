<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Router;

use League\Container\ContainerInterface;
use League\Pipeline\Pipeline;
use PhpSpec\ObjectBehavior;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Request;
use Refinery29\Piston\RequestFactory;
use Refinery29\Piston\Router\MiddlewareStrategy;
use Refinery29\Piston\Router\Route;
use Refinery29\Piston\Router\RouteGroup;
use spec\Refinery29\Piston\Stub\FooController;

/**
 * @mixin MiddlewareStrategy
 */
class MiddlewareStrategySpec extends ObjectBehavior
{
    public function let(ContainerInterface $container)
    {
        $container->get('Request')->willReturn(RequestFactory::createFromUri('/alias'));
        $container->get('Response')->willReturn(new ApiResponse());
        $container->get(FooController::class)->willReturn(new FooController());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(MiddlewareStrategy::class);
    }

    public function it_can_dispatch_controller(Route $route, FooController $foo, Request $request, ApiResponse $response)
    {
        $route->getParentGroup()->willReturn(false);
        $route->getPipeline()->willReturn(new Pipeline());
        $foo->test($request, $response, [])->willReturn($response);

        $this->setResponse($response)
            ->setRequest($request);

        $this->dispatch(
            [$foo, 'test'],
            [],
            $route
        )->shouldHaveType(ApiResponse::class);
    }

    public function it_handles_group_middleware(RouteGroup $group, Route $route, FooController $foo, Request $request, ApiResponse $response)
    {
        $route->getParentGroup()->willReturn($group);
        $route->getPipeline()->willReturn(new Pipeline());

        $group->getPipeline()->willReturn(new Pipeline());

        $group->getPipeline()->shouldBeCalled();

        $this->setResponse($response)
            ->setRequest($request);

        $this->getResponse()->shouldReturn($response);

        $this->dispatch(
            [$foo, 'test'],
            [],
            $route
        );
    }

    public function it_handles_route_middleware(Route $route, FooController $foo, Request $request, ApiResponse $response)
    {
        $route->getParentGroup()->willReturn(false);
        $route->getPipeline()->willReturn(new Pipeline());

        $route->getPipeline()->shouldBeCalled();

        $this->setResponse($response)
            ->setRequest($request);

        $this->getResponse()->shouldReturn($response);

        $this->dispatch(
            [$foo, 'test'],
            [],
            $route
        );
    }
}
