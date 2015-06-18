<?php

namespace Refinery29\Piston\Request\Filters;

use Refinery29\Piston\Request\Request;

class Fields implements Filter
{
    public static function apply(Request $request)
    {
        $fields = $request->get('fields');
        $fields = explode(',', $fields);

        $request->setRequestedFields($fields);

        return $request;
    }
}
