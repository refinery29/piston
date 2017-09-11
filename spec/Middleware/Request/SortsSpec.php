<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Middleware\Request;

use League\Route\Http\Exception\BadRequestException;
use PhpSpec\ObjectBehavior;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Middleware\Request\Sorts;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;

/**
 * @mixin Sorts
 */
class SortsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Sorts::class);
    }

    public function it_will_get_sort(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withQueryParams([
            'sort' => \implode(',', [
                '-created',
                'title',
            ]),
        ]);

        $result = $this->__invoke(new Payload($middleware->getWrappedObject(), $request, new ApiResponse()))->getRequest();

        $result->shouldHaveType(Request::class);

        $result->getSort('created')->shouldBe(Sorts::SORT_DESCENDING);
        $result->getSort('title')->shouldBe(Sorts::SORT_ASCENDING);
        $result->getSort('not-set')->shouldBe(null);
    }

    public function it_does_not_throw_exception_with_no_sort(Piston $middleware)
    {
        $result = $this->__invoke(new Payload(
            $middleware->getWrappedObject(),
            new Request(),
            new ApiResponse()
        ))->getRequest();

        $result->shouldHaveType(Request::class);
    }

    public function it_throws_exception_on_empty_sort_string(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withMethod('GET')->withQueryParams(['sort' => '']);

        $payload = new Payload($middleware->getWrappedObject(), $request, new ApiResponse());

        $this->shouldThrow(BadRequestException::class)->during__Invoke($payload);
    }

    public function it_throws_exception_on_empty_descending_sort(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withMethod('GET')->withQueryParams(['sort' => '-']);

        $payload = new Payload($middleware->getWrappedObject(), $request, new ApiResponse());

        $this->shouldThrow(BadRequestException::class)->during__Invoke($payload);
    }

    public function it_throws_exception_on_empty_sort_name_with_valid_sort_name(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withMethod('GET')->withQueryParams(['sort' => 'foo,']);

        $payload = new Payload($middleware->getWrappedObject(), $request, new ApiResponse());

        $this->shouldThrow(BadRequestException::class)->during__Invoke($payload);
    }

    public function it_does_not_ensure_get_only_request_when_no_sorts_are_included(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withMethod('PATCH');

        $result = $this->__invoke(new Payload($middleware->getWrappedObject(), $request, new ApiResponse()))->getRequest();

        $result->shouldHaveType(Request::class);
    }

    public function it_ensures_get_only_request_when_sorts_are_included(Piston $middleware)
    {
        /** @var Request $request */
        $request = (new Request())->withMethod('PUT')->withQueryParams(['sort' => '-foo']);

        $payload = new Payload($middleware->getWrappedObject(), $request, new ApiResponse());

        $this->shouldThrow(BadRequestException::class)->during__Invoke($payload);
    }
}
