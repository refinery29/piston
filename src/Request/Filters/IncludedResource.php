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
    static public function apply(Request $request)
    {
        return $request;
    }

}