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
use Refinery29\Piston\Middleware\Request\Pagination\OffsetLimitPagination;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;
use Refinery29\Piston\RequestFactory;

/**
 * @mixin OffsetLimitPagination
 */
class OffsetLimitPaginationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(OffsetLimitPagination::class);
    }

    public function it_will_not_allow_pagination_on_non_get_requests(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => 20, 'offset' => 40])->withMethod('PUT');

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_returns_a_payload_with_request(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => 20, 'offset' => 40]);

        $response = $this->__invoke($this->getPayload($request, $piston));
        $response->shouldHaveType(Payload::class);
        $response->getRequest()->shouldBeAnInstanceOf(Request::class);
    }

    public function it_does_not_allow_usage_with_multiple_pagination_strategies(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => 20, 'offset' => 40]);
        $request->setBeforeCursor('abc');

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_assigns_limit_to_request_as_integer(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '20', 'offset' => '40']);
        $request = $this->__invoke($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 40, 'limit' => 20]);
    }

    public function it_assigns_offset_to_request_as_integer(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '20', 'offset' => '40']);
        $request = $this->__invoke($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 40, 'limit' => 20]);
    }

    public function it_assigns_default_limit_of_ten_when_none_given(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['offset' => '20']);
        $request = $this->__invoke($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 20, 'limit' => 10]);
    }

    public function it_assigns_default_offset_of_zero_when_none_given(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '20']);
        $request = $this->__invoke($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 0, 'limit' => 20]);
    }

    public function it_throws_if_offset_is_not_numeric(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['offset' => '20', 'limit' => 'nope']);

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_throws_if_limit_is_not_numeric(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '20', 'offset' => 'nope']);
        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_throws_if_offset_is_not_an_integer(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '20', 'offset' => '10.34']);

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_throws_if_limit_is_not_an_integer(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => '10.35', 'offset' => '20']);

        $this->shouldThrow(BadRequestException::class)->during('__invoke', [$this->getPayload($request, $piston)]);
    }

    public function it_will_not_allow_previously_paginated_requests(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['limit' => 123]);
        $request->setBeforeCursor(\rand());

        $this->shouldThrow(BadRequestException::class)->during__Invoke($this->getPayload($request, $piston));
    }

    private function getPayload($request, Piston $piston)
    {
        return new Payload($piston->getWrappedObject(), $request, new ApiResponse());
    }
}
