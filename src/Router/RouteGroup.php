<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Router;

use Refinery29\Piston\Middleware\HasMiddleware;
use Refinery29\Piston\Middleware\HasMiddlewareTrait;

class RouteGroup extends \League\Route\RouteGroup implements HasMiddleware
{
    use HasMiddlewareTrait;
}
