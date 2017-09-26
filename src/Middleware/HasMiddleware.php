<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware;

use League\Pipeline\StageInterface;

interface HasMiddleware
{
    /**
     * @param StageInterface $stage
     *
     * @return $this
     */
    public function addMiddleware($stage);
}
