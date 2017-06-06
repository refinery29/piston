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
use Refinery29\Piston\Middleware\Request\Pagination\PagedPagination;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;
use Refinery29\Piston\RequestFactory;

class PagedPaginationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(PagedPagination::class);
    }

    public function it_returns_a_payload_with_request(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 2, 'per-page' => 10]);

        $response = $this->process($this->getPayload($request, $piston));
        $response->shouldHaveType(Payload::class);
        $response->getRequest()->shouldBeAnInstanceOf(Request::class);
    }

    public function it_assigns_per_page_to_request_as_integer(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 2, 'per-page' => '5']);
        $request = $this->process($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 5, 'limit' => 5]);
    }

    public function it_assigns_default_per_page_of_ten_when_none_given(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 3]);
        $request = $this->process($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 20, 'limit' => 10]);
    }

    public function it_assigns_default_page_of_one_when_none_given(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['per-page' => 5]);
        $request = $this->process($this->getPayload($request, $piston));

        $request->getRequest()->getOffsetLimit()->shouldBe(['offset' => 0, 'limit' => 5]);
    }

    public function it_throws_if_page_is_not_numeric(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 'foo', 'per-page' => 10]);

        $this->shouldThrow(BadRequestException::class)->during('process', [$this->getPayload($request, $piston)]);
    }

    public function it_throws_if_per_page_is_not_numeric(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 2, 'per-page' => 'foo']);
        $this->shouldThrow(BadRequestException::class)->during('process', [$this->getPayload($request, $piston)]);
    }

    public function it_will_not_allow_previously_paginated_requests(Piston $piston)
    {
        $request = RequestFactory::fromGlobals()->withQueryParams(['page' => 1]);
        $request->setBeforeCursor(\rand());

        $this->shouldThrow(BadRequestException::class)->duringprocess($this->getPayload($request, $piston));
    }

    private function getPayload($request, Piston $piston)
    {
        return new Payload($piston->getWrappedObject(), $request, new ApiResponse());
    }
}
