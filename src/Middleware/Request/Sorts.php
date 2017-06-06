<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware\Request;

use League\Pipeline\StageInterface;
use League\Route\Http\Exception\BadRequestException;
use Refinery29\Piston\Middleware\GetOnlyStage;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Request;

class Sorts implements StageInterface
{
    use GetOnlyStage;

    const SORT_ASCENDING = 'ASC';
    const SORT_DESCENDING = 'DESC';

    /**
     * @param Payload $payload
     *
     * @throws \League\Route\Http\Exception\BadRequestException
     *
     * @return Payload
     */
    public function process($payload)
    {
        /** @var Request $request */
        $request = $payload->getRequest();

        if (!isset($request->getQueryParams()['sort'])) {
            return $payload;
        }

        $this->ensureGetOnlyRequest($request);

        $providedSorts = \explode(',', $request->getQueryParams()['sort']);

        if (empty($providedSorts)) {
            return $payload;
        }

        $sorts = [];

        foreach ($providedSorts as $sort) {
            if (\strlen($sort) <= 0 || $sort === '-') {
                throw new BadRequestException('Sort parameter cannot be empty.');
            }

            if ($sort[0] === '-') {
                $sorts[\substr($sort, 1)] = self::SORT_DESCENDING;
                continue;
            }

            $sorts[$sort] = self::SORT_ASCENDING;
        }

        return $payload->withRequest(
            $request->withSorts($sorts)
        );
    }
}
