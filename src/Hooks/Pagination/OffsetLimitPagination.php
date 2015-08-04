<?php namespace Refinery29\Piston\Hooks\Pagination;

use League\Route\Http\Exception\BadRequestException;
use Refinery29\Piston\Http\Request;

class OffsetLimitPagination extends PaginationHook
{
    protected $default_offset = 0;

    protected $default_limit = 10;

    /**
     * @param Request $request
     * @throws BadRequestException
     * @return Request
     */
    public function process($request)
    {
        $offset = $request->get('offset');
        $limit = $request->get('limit');

        if (!$offset && !$limit) {
            return $request;
        }

        parent::process($request);

        $offset = $this->coerceToInteger($offset, 'offset') ?: $this->default_offset;
        $limit = $this->coerceToInteger($limit, 'limit') ?: $this->default_limit;

        $request->setOffsetLimit($offset, $limit);

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
