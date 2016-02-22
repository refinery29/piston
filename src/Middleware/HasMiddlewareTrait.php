<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware;

use League\Pipeline\StageInterface;

trait HasMiddlewareTrait
{
    /**
     * @var ExceptionalPipeline
     */
    protected $pipeline;

    /**
     * @var StageInterface[]
     */
    private $stages = [];

    /**
     * @param StageInterface $stage
     *
     * @return $this
     */
    public function addMiddleware(StageInterface $stage)
    {
        $this->pipeline = $this->getPipeline()->pipe($stage);

        return $this;
    }

    /**
     * Instaniate a new pipeline if one doesn't exist.
     *
     * @return ExceptionalPipeline
     */
    public function getPipeline()
    {
        if (!$this->pipeline) {
            $this->pipeline = new ExceptionalPipeline();
        }

        return $this->pipeline;
    }
}
