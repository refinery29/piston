<?php namespace Refinery29\Piston\Request\Filters;

use Refinery29\Piston\Request\Request;

class Pagination implements Filter
{
    /**
     * @param Request $request
     * @return Request
     */
    public static function apply(Request $request)
    {
        $cursor = $request->get('cursor');

        return $request;
    }
}
