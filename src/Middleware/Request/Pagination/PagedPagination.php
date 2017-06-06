<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware\Request\Pagination;

use Assert\Assertion;
use League\Pipeline\StageInterface;
use League\Route\Http\Exception\BadRequestException;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Request;

class PagedPagination implements StageInterface
{
    use SinglePagination;

    /**
     * @var int
     */
    protected $defaultPage = 1;

    /**
     * @var int
     */
    protected $defaultPerPage = 10;

    /**
     * @param int $defaultPerPage
     */
    public function __construct($defaultPerPage = 10)
    {
        Assertion::integer($defaultPerPage);
        $this->defaultPerPage = $defaultPerPage;
    }

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

        $page = (isset($queryParams['page'])) ? $this->coerceToInteger($queryParams['page'], 'page') : null;
        $perPage = (isset($queryParams['per-page'])) ? $this->coerceToInteger($queryParams['per-page'], 'per-page') : null;

        if ($page || $perPage) {
            $this->ensureNotPreviouslyPaginated($request);

            $page = $page ?: $this->defaultPage;
            $perPage = $perPage ?: $this->defaultPerPage;

            return $payload->withRequest(
                $request->withPageAndPerPage($page, $perPage)
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
