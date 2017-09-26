<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Middleware\Request;

use PhpSpec\ObjectBehavior;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Middleware\Request\RequestedFields;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;

/**
 * @mixin RequestedFields
 */
class RequestedFieldsSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RequestedFields::class);
    }

    public function it_will_get_requested_fields(Piston $middleware)
    {
        $request = (new Request())->withQueryParams(['fields' => 'one,two,three']);

        $subject = new Payload($middleware->getWrappedObject(), $request, new ApiResponse());

        $result = $this->__invoke($subject);
        $result = $result->getRequest();

        $resources = $result->getRequestedFields();
        $resources->shouldBeArray();

        $resources->shouldContain('one');
        $resources->shouldContain('two');
        $resources->shouldContain('three');
    }
}
