<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Middleware\Request;

use League\Pipeline\Pipeline;
use League\Pipeline\PipelineBuilder;
use League\Pipeline\StageInterface;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Middleware\Request\Pagination\CursorBasedPagination;
use Refinery29\Piston\Middleware\Request\Pagination\OffsetLimitPagination;
use Refinery29\Piston\Middleware\Request\Pagination\PagedPagination;

class RequestPipeline implements StageInterface
{
    /**
     * @var Pipeline
     */
    protected $pipeline;

    /**
     * @var PipelineBuilder
     */
    protected $builder;

    /**
     * @param PipelineBuilder $builder
     */
    public function __construct(PipelineBuilder $builder = null)
    {
        $this->builder = $builder ?: new PipelineBuilder();
    }

    /**
     * @param Payload $payload
     *
     * @return Payload
     */
    public function process($payload)
    {
        $this->builder
            ->add(new IncludedResource())
            ->add(new RequestedFields())
            ->add(new PagedPagination())
            ->add(new OffsetLimitPagination())
            ->add(new CursorBasedPagination())
            ->add(new Sorts());

        $this->pipeline = $this->builder->build();

        return $this->pipeline->process($payload);
    }
}
