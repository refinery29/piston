<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Router;

use League\Route\Route as BaseRoute;
use League\Route\Strategy\RequestResponseStrategy;
use League\Route\Strategy\StrategyInterface;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Middleware\Payload;
use Refinery29\Piston\Middleware\PipelineProcessor;

class MiddlewareStrategy extends RequestResponseStrategy implements StrategyInterface
{
    /**
     * @param callable|string $controller
     * @param array           $vars
     * @param BaseRoute       $route
     *
     * @throws \Exception
     *
     * @return ApiResponse
     */
    public function dispatch(callable $controller, array $vars = [], BaseRoute $route = null)
    {
        if ($group = $route->getParentGroup()) {
            $this->response = (new PipelineProcessor())
                ->handlePayload(new Payload($group, $this->request, $this->response))
                ->getResponse();
        }

        $this->response = (new PipelineProcessor())
            ->handlePayload(new Payload($route, $this->request, $this->response))
            ->getResponse();

        return \call_user_func_array(
            $controller,
            [$this->request, $this->response, $vars]
        );
    }
}
