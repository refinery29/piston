<?php namespace Refinery29\Piston\Hooks\Pagination;

use League\Route\Http\Exception\BadRequestException;
use Refinery29\Piston\Hooks\GetOnlyHook;
use Refinery29\Piston\Http\Request;

class OffsetLimitPagination
{
    use SinglePaginationHook, GetOnlyHook;

    /**
     * @var int $default_offset
     */
    protected $default_offset = 0;

    /**
     * @var int $default_limit
     */
    protected $default_limit = 10;

    /**
     * @param Request $request
     * @throws BadRequestException
     * @return Request
     */
    public function process($request)
    {
        $this->ensureNotPreviouslyPaginated($request);

        $offset = $this->coerceToInteger($request->get('offset'), 'offset');
        $limit = $this->coerceToInteger($request->get('limit'), 'limit');

        if ($offset || $limit) {
            $this->ensureGetOnlyRequest($request);

            $offset = $offset ?: $this->default_offset;
            $limit = $limit ?: $this->default_limit;
            $request->setOffsetLimit($offset, $limit);
        }

        return $request;
    }

    /**
     * @param mixed $param
     * @param string $param_name
     * @throws BadRequestException
     * @return int
     */
    private function coerceToInteger($param, $param_name)
    {
        if (!$param) {
            return;
        }

        if (is_numeric($param)) {
            $integer_value = intval($param);

            if ($integer_value == floatval($param)) {
                return $integer_value;
            }
        }

        throw new BadRequestException('Parameter "' . $param_name . '" must be an integer. Got ' . $param);
    }
}