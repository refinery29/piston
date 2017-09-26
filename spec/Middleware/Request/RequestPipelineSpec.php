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
use Refinery29\Piston\Middleware\Request\RequestPipeline;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;

/**
 * @mixin RequestPipeline
 */
class RequestPipelineSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RequestPipeline::class);
    }

    public function it_can_be_processed(Piston $middleware)
    {
        $request = new Request();
        $response = new ApiResponse();
        $subject = new Payload($middleware->getWrappedObject(), $request, $response);

        $this->__invoke($subject)->shouldHaveType(Payload::class);
    }
}
