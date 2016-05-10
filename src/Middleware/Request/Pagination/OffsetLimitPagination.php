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
        $offset = $this->getOffset($request->getQueryParams());
        $limit = $this->getLimit($request->getQueryParams());

        if ($offset || $limit) {
            $this->ensureNotPreviouslyPaginated($request);
            $this->ensureGetOnlyRequest($request);

            $offset = $offset ?: $this->defaultOffset;
            $limit = $limit ?: $this->defaultLimit;
            $request->setOffsetLimit($offset, $limit);
        }

        return $payload;
    }

    /**
     * @param mixed  $param
     * @param string $paramName
     *
     * @throws BadRequestException
     *
     * @return int
     */
    private function coerceToInteger($param, $paramName)
    {
        if (is_numeric($param)) {
            $integerValue = intval($param);

            if ($integerValue == floatval($param)) {
                return $integerValue;
            }
        }

        throw new BadRequestException(
            'Parameter "' . $paramName . '" must be an integer. Got ' . $param
        );
    }

    /**
     * @param array $queryParams
     *
     * @throws BadRequestException
     *
     * @return int|null
     */
    private function getOffset(array $queryParams = [])
    {
        $offset = null;

        if (isset($queryParams['offset'])) {
            $offset = $this->coerceToInteger($queryParams['offset'], 'offset');
        }

        return $offset;
    }

    /**
     * @param array $queryParams
     *
     * @throws BadRequestException
     *
     * @return int|null
     */
    private function getLimit(array $queryParams = [])
    {
        $limit = null;

        if (isset($queryParams['limit'])) {
            $limit = $this->coerceToInteger($queryParams['limit'], 'limit');
        }

        return $limit;
    }
}
