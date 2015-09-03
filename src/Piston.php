<?php

namespace Refinery29\Piston;

use ArrayAccess;
use Kayladnls\Seesaw\Route;
use Kayladnls\Seesaw\Seesaw;
use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider;
use Refinery29\Piston\Http\Pipeline\RequestPipeline;
use Refinery29\Piston\Http\Pipeline\ResponsePipeline;
use Refinery29\Piston\Http\Request;
use Refinery29\Piston\Http\ResponseNegotiator;
use Refinery29\Piston\Pipeline\HasPipelines;
use Refinery29\Piston\Pipeline\LifeCyclePipelines;
use Refinery29\Piston\Router\PistonStrategy;
use Refinery29\Piston\Router\Routes\RouteGroup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Piston implements ContainerAwareInterface, HasPipelines
{
    use LifeCyclePipelines;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Seesaw
     */
    protected $router;

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var array
     */
    protected $config;

    protected $response;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container ?: new Container();
        $this->container['app'] = $this;

        $this->bootstrapRouter();
        $this->bootstrapPipelines();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request ? $this->request : Request::createFromGlobals();
    }

    public function getResponse(Request $request)
    {
        $negotiator = new ResponseNegotiator($request);

        $this->response = $negotiator->negotiateResponse();
    }

    /**
     * @param Route $route
     *
     * @return Piston
     */
    public function addRoute(Route $route)
    {
        $this->router->add($route);

        return $this;
    }

    /**
     * @param RouteGroup $group
     *
     * @return $this
     */
    public function addRouteGroup(RouteGroup $group)
    {
        $this->router->addGroup($group);

        return $this;
    }

    /**
     * @param string $name
     * @param Route  $route
     */
    public function addNamedRoute($name, Route $route)
    {
        $this->router->addNamedRoute($name, $route->getVerb(), $route->getUrl(), $route->getAction());
    }

    /**
     * @return Response
     */
    public function launch()
    {
        $dispatcher = $this->router->getDispatcher();

        $this->loadContainer();

        $response = $dispatcher->dispatch($this->request->getMethod(), $this->request->getPathInfo());

        $response = $this->postProcessResponse($response);

        return $response->send();
    }

    protected function loadContainer()
    {
        $this->preProcessRequest();
        $this->getResponse($this->request);

        $this->container->add('Request', $this->request, true);
        $this->container->add('Response', $this->response, true);
    }

    protected function preProcessRequest()
    {
        return (new RequestPipeline())->process($this->request);
    }

    protected function postProcessResponse($response)
    {
        return (new ResponsePipeline())->process($this->response);
    }

    /**
     * @param ServiceProvider $service_provider
     */
    public function register(ServiceProvider $service_provider)
    {
        $this->container->addServiceProvider($service_provider);
    }

    public function addDecorator(Decorator $decorator)
    {
        $app = $decorator->register();

        if (!$app instanceof Piston) {
            throw new \InvalidArgumentException('Decorators Must Return an Instance of Piston');
        }

        return $app;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function bootstrapRouter()
    {
        $this->router = new Seesaw(null, null, $this->container);
        $this->router->setStrategy(new PistonStrategy());
        $this->container->add('PistonRouter', $this->router, true);
    }

    /**
     * @return Response
     */
    public function notFound()
    {
        return new Response('', 404);
    }

    /**
     * @param $url
     *
     * @return RedirectResponse
     */
    public static function redirect($url)
    {
        return new RedirectResponse($url);
    }
}
