<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston\Stub;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\EmitterInterface;

class PassThroughEmitter implements EmitterInterface
{
    /**
     * Emit a response.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function emit(ResponseInterface $response)
    {
        return $response;
    }
}
