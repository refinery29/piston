<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware;

use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Request;

class Payload
{
    /**
     * @var HasMiddleware
     */
    private $subject;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(HasMiddleware $subject, Request $request, ApiResponse $response)
    {
        $this->subject = $subject;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return HasMiddleware
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ApiResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Request $request
     *
     * @return Payload
     */
    public function withRequest(Request $request)
    {
        $new = clone $this;
        $new->request = $request;

        return $new;
    }

    /**
     * @param ApiResponse $response
     *
     * @return Payload
     */
    public function withResponse(ApiResponse $response)
    {
        $new = clone $this;
        $new->response = $response;

        return $new;
    }

    /**
     * @param HasMiddleware $subject
     *
     * @return Payload
     */
    public function withSubject(HasMiddleware $subject)
    {
        $new = clone $this;
        $new->subject = $subject;

        return $new;
    }
}
