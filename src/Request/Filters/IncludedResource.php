<?php

namespace Refinery29\Piston\Request\Filters;

use Refinery29\Piston\Request\Request;

class IncludedResource implements Filter
{
    /**
     * @param Request $request
     *
     * @return Request
     */
    public static function apply(Request $request)
    {
        return $request;
    }
}
