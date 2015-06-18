<?php

namespace Refinery29\Piston\Request\Filters;

use Refinery29\Piston\Request\Request;

class Pagination implements Filter
{
    /**
     * @param Request $request
     *
     * @return Request
     */
    static public function apply(Request $request)
    {
        $cursor = $request->get('cursor');

        // TODO

        return $request;
    }
}