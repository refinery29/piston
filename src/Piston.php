<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston;

use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider;
use League\Route\Http\Exception\NotFoundException;
use League\Route\RouteCollection;
use Psr\Http\Message\RequestInterface;
use Refinery29\Piston\Router\MiddlewareStrategy;
use Refinery29\Piston\Router\Route;
use Refinery29\Piston\Router\RouteGroup;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitter;

class Piston extends RouteCollection implements Middleware\HasMiddleware
{
    use Middleware\HasMiddlewareTrait;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var ApiResponse
     */
    private $response;

    /**
     * @var EmitterInterface
     */
    private $emitter;

    /**
     * @var callable[]
     */
    private $exceptions = [];

    /**
     * @param ContainerInterface $container
     * @param RequestInterface   $request
     * @param EmitterInterface   $emitter
     */
    public function __construct(
        ContainerInterface $container = null,
        RequestInterface $request = null,
        EmitterInterface $emitter = null
    ) {
        $this->container = $container ?: new Container();
        $this->request = $request ?: RequestFactory::fromGlobals();
        $this->emitter = $emitter ?: new SapiEmitter();
        $this->response = new ApiResponse();

        $this->loadContainer();
        parent::__construct($this->container);

        $this->setStrategy(new MiddlewareStrategy($this->container));

        $this->registerNotFoundExceptionHandler();
    }

    private function registerNotFoundExceptionHandler()
    {
        $this->registerException(NotFoundException::class, function (Piston $piston) {
            return $piston->notFound();
        });
    }

    /**
     * Add a group of routes to the collection.
     *
     * @param string   $prefix
     * @param callable $group
     *
     * @return RouteGroup
     */
    public function group($prefix, callable $group)
    {
        $group = new RouteGroup($prefix, $group, $this);

        $this->groups[] = $group;

        return $group;
    }

    /**
     * {@inheritdoc}
     */
    public function map($method, $path, $handler)
    {
        $path = \sprintf('/%s', \ltrim($path, '/'));

        $route = (new Route())
            ->setMethods((array) $method)
            ->setPath($path)
            ->setCallable($handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * @throws \Exception
     *
     * @return ApiResponse
     */
    public function launch()
    {
        try {
            $this->processPipeline();

            $this->response = $this->dispatch($this->request, $this->response);

            if ($this->response instanceof CompiledResponse) {
                $this->response->compileContent();
            }

            return $this->emitter->emit($this->response);
        } catch (\Exception $exception) {
            foreach ($this->exceptions as $exceptionClassName => $callable) {
                if ($exception instanceof $exceptionClassName) {
                    return $callable($this, $exception);
                }
            }

            throw $exception;
        }
    }

    public function notFound()
    {
        return $this->getErrorResponse(404);
    }

    /**
     * @param string $code
     * @param string $body
     */
    public function getErrorResponse($code, $body = '{}')
    {
        $this->response->getBody()->write($body);

        return $this->emitter->emit($this->response->withStatus($code));
    }

    /**
     * @param ServiceProvider\AbstractServiceProvider $serviceProvider
     */
    public function register(ServiceProvider\AbstractServiceProvider $serviceProvider)
    {
        $this->container->addServiceProvider($serviceProvider);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    private function processPipeline()
    {
        $payload = (new Middleware\PipelineProcessor())
            ->handlePayload($this->buildPayload());

        $this->request = $payload->getRequest();
        $this->response = $payload->getResponse();
    }

    private function loadContainer()
    {
        $payload = (new Middleware\Request\RequestPipeline())
            ->process($this->buildPayload());

        $this->request = $payload->getRequest();
        $this->response = $payload->getResponse();

        $this->container->add(Request::class, $this->request, true);
        $this->container->add(ApiResponse::class, $this->response, true);
    }

    /**
     * @return Middleware\Payload
     */
    private function buildPayload()
    {
        return new Middleware\Payload($this, $this->request, $this->response);
    }

    /**
     * @param string   $exceptionClassName
     * @param callable $callback
     */
    public function registerException($exceptionClassName, callable $callback)
    {
        $this->exceptions[$exceptionClassName] = $callback;
    }
}
