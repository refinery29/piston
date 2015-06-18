<?php

namespace Refinery29\Piston;

use Dotenv\Dotenv;
use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider;
use League\Route\RouteCollection;
use League\Route\Strategy\RequestResponseStrategy;
use Refinery29\Piston\Hooks\Hookable;
use Refinery29\Piston\Hooks\Worker;
use Refinery29\Piston\Request\Filters\Fields;
use Refinery29\Piston\Request\Filters\IncludedResource;
use Refinery29\Piston\Request\Filters\Pagination;
use Refinery29\Piston\Request\Request as PistonRequest;
use Refinery29\Piston\Routes\Route;
use Refinery29\Piston\ServiceProviders\SpotDbProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Piston
{
    use Hookable;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RouteCollection
     */
    protected $router;

    /**
     * @var PistonRequest
     */
    protected $request;
    
    public function __construct(ContainerInterface $container = null, $config_dir)
    {
        $this->container = $container ?: new Container();

        $this->loadConfig($config_dir);

        $this->bootstrapServiceProviders();

        $this->bootstrapRouter();
        $this->bootstrapHooks();

        $this->container->add('app', $this);
    }

    public function setRequest(PistonRequest $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        $request = $this->request ?: PistonRequest::createFromGlobals();

        $request = Pagination::apply($request);
        $request = IncludedResource::apply($request);
        $request = Fields::apply($request);

        $this->container->add('Symfony\Component\HttpFoundation\Request', $request, true);

        return $request;
    }

    public function addRoute(Route $route)
    {
        $this->router->addRoute($route->getVerb(), $route->getAlias(), $route->getAction());
    }

    public function launch()
    {
        $dispatcher = $this->router->getDispatcher();

        $request = $this->getRequest();

        $response = Worker::work($this->getPreHooks(), $request, new Response());

        $response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response = Worker::work($this->getPostHooks(), $request, $response);

        return $response->send();
    }

    public function addServiceProvider(ServiceProvider $service_provider)
    {
        $this->container->addServiceProvider($service_provider);
    }

    public function getContainer()
    {
        return $this->container;
    }

    private function bootstrapRouter()
    {
        $this->router = new RouteCollection($this->container);
        $this->router->setStrategy(new RequestResponseStrategy($this->container));
    }

    private function bootstrapServiceProviders()
    {
        $this->container->addServiceProvider(new SpotDbProvider());
    }

    private function loadConfig($config_dir)
    {
        $env = new Dotenv($config_dir);
        $env->load();
    }

    public function notFound()
    {
        return new Response('', 404);
    }

    public function redirect($url)
    {
        return new RedirectResponse($url);
    }
}
