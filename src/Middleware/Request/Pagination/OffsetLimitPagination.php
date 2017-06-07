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

class OffsetLimitPagination implements StageInterface
{
    use SinglePagination;
    use GetOnlyStage;

    /**
     * @var int
     */
    protected $defaultOffset = 0;

    /**
     * @var int
     */
    protected $defaultLimit = 10;

    /**
     * @param Payload $payload
     *
     * @throws BadRequestException
     *
     * @return Payload
     */
    public function process($payload)
    {
        /** @var Request $request */
        $request = $payload->getRequest();

        $queryParams = $request->getQueryParams();

        $offset = isset($queryParams['offset']) ? $this->coerceToInteger($queryParams['offset'], 'offset') : null;
        $limit = isset($queryParams['limit']) ? $this->coerceToInteger($queryParams['limit'], 'limit') : null;

        if ($offset || $limit) {
            $this->ensureNotPreviouslyPaginated($request);
            $this->ensureGetOnlyRequest($request);

            $offset = $offset ?: $this->defaultOffset;
            $limit = $limit ?: $this->defaultLimit;

            return $payload->withRequest(
                $request->withOffsetLimit($offset, $limit)
            );
        }

        return $payload;
    }

    /**
     * @param mixed  $param
     * @param string $param_name
     *
     * @throws BadRequestException
     *
     * @return int
     */
    private function coerceToInteger($param, $param_name)
    {
        if (\is_numeric($param)) {
            $integer_value = (int) $param;

            if ($integer_value == (float) $param) {
                return $integer_value;
            }
        }

        throw new BadRequestException('Parameter "' . $param_name . '" must be an integer. Got ' . $param);
    }
}
