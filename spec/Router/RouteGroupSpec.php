<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Router;

use League\Pipeline\Pipeline;
use League\Pipeline\StageInterface;
use League\Route\Route;
use League\Route\RouteCollection;
use League\Route\RouteGroup;
use PhpSpec\ObjectBehavior;

/**
 * @mixin RouteGroup
 */
class RouteGroupSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            '/yolo',
            function ($router) {
            },
            new RouteCollection()
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RouteGroup::class);
    }

    public function it_can_add_middleware(StageInterface $operation)
    {
        $this->addMiddleware($operation);
        $this->getPipeline()->shouldHaveType(Pipeline::class);
    }

    public function it_can_map_a_route()
    {
        $this->map('GET', 'path', 'handler')->shouldHaveType(Route::class);
    }

    public function it_sets_itself_as_parent_group()
    {
        $this->map('GET', 'path', 'handler')->getParentGroup()->shouldReturn($this);
    }
}
