<?php

namespace Refinery29\Piston\Request\Filters;

use Refinery29\Piston\Request\Request;

interface Filter
{
    /**
     * @param Request $request
     *
     * @return Request
     */
    static public function apply(Request $request);
}
