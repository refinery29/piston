<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Middleware\Request\Pagination;

use League\Route\Http\Exception\BadRequestException;
use PhpSpec\ObjectBehavior;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Middleware\Request\Pagination\CursorBasedPagination;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;
use Refinery29\Piston\RequestFactory;

/**
 * @mixin CursorBasedPagination
 */
class CursorBasedPaginationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(CursorBasedPagination::class);
    }

    public function it_will_not_allow_pagination_on_non_get_requests(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withMethod('PUT')->withQueryParams(['before' => 123]);

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $middleware)]);
    }

    public function it_will_not_allow_before_an_after(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['before' => 123, 'after' => 456]);

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $middleware)]);
    }

    public function it_will_allow_before_cursor_on_get_requests(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['before' => 123]);
        $this->__invoke($this->getPayload($request, $middleware))
            ->getRequest()
            ->getPaginationCursor()->shouldReturn(['before' => 123]);
    }

    public function it_will_allow_after_cursor_on_get_requests(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['after' => 123]);
        $this->__invoke($this->getPayload($request, $middleware))
            ->getRequest()
            ->getPaginationCursor()->shouldReturn(['after' => 123]);
    }

    public function it_returns_a_payload_with_request(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['before' => 123]);

        $response = $this->__invoke($this->getPayload($request, $middleware));
        $response->shouldHaveType(Payload::class);
        $response->getRequest()->shouldBeAnInstanceOf(Request::class);
    }

    public function it_will_not_allow_previously_paginated_requests(Piston $middleware)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['before' => 123]);
        $request->setOffsetLimit(10, 10);

        $this->shouldThrow(BadRequestException::class)->during__Invoke($this->getPayload($request, $middleware));
    }

    private function getPayload($request, Piston $middleware)
    {
        return new Payload($middleware->getWrappedObject(), $request, new ApiResponse());
    }
}
