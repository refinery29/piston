<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware\Request;

use League\Pipeline\StageInterface;
use Refinery29\Piston\Middleware\GetOnlyStage;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Request;

class RequestedFields implements StageInterface
{
    use GetOnlyStage;

    /**
     * @param Payload $payload
     *
     * @return Payload
     */
    public function process($payload)
    {
        /* @var Request $request */
        $request = $payload->getRequest();

        if (!isset($request->getQueryParams()['fields'])) {
            return $payload;
        }

        $fields = $request->getQueryParams()['fields'];

        if ($fields) {
            $this->ensureGetOnlyRequest($request);
            $fields = explode(',', $fields);
        }

        if (!empty($fields)) {
            $payload = new Payload(
                $payload->getSubject(),
                $request->withRequestedFields((array) $fields),
                $payload->getResponse()
            );
        }

        return $payload;
    }
}
