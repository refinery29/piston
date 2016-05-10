<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware\Request\Pagination;

use League\Pipeline\StageInterface;
use League\Route\Http\Exception\BadRequestException;
use Refinery29\Piston\Middleware\GetOnlyStage;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Request;

class CursorBasedPagination implements StageInterface
{
    use GetOnlyStage;
    use SinglePagination;

    /**
     * @param Payload $payload
     *
     * @throws BadRequestException
     *
     * @return Payload
     */
    public function process($payload)
    {
        /* @var Request $request */
        $request = $payload->getRequest();
        $before = $this->getBefore($request->getQueryParams());
        $after = $this->getAfter($request->getQueryParams());

        if ($before && $after) {
            throw new BadRequestException('You may not specify both before and after');
        }

        if ($before || $after) {
            $this->ensureNotPreviouslyPaginated($request);
            $this->ensureGetOnlyRequest($request);

            if ($before) {
                $request->setBeforeCursor($before);

                return $payload;
            }

            if ($after) {
                $request->setAfterCursor($after);

                return $payload;
            }
        }

        return $payload;
    }

    /**
     * @param array $queryParams
     *
     * @return null|string
     */
    private function getBefore($queryParams)
    {
        return (isset($queryParams['before']))
            ? $queryParams['before']
            : null;
    }

    /**
     * @param array $queryParams
     *
     * @return null|string
     */
    private function getAfter($queryParams)
    {
        return (isset($queryParams['after']))
            ? $queryParams['after']
            : null;
    }
}
