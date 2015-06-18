<?php namespace spec\Refinery29\Piston;

use League\Container\Container;
use League\Container\ContainerInterface;
use League\Container\ServiceProvider;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Refinery29\Piston\Request\Request;
use Refinery29\Piston\Router\Routes\Route;

class PistonSpec extends ObjectBehavior
{
    public function let(Container $container)
    {
        $container->beADoubleOf('League\Container\Container');
        $this->beConstructedWith($container, __DIR__);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Piston');
    }

    public function it_should_create_container_if_none_is_injected()
    {
        $this->beConstructedWith(null, __DIR__);
        $this->getContainer()->shouldHaveType('League\Container\Container');
    }

    public function it_can_add_a_route(Route $route)
    {
        $route->beADoubleOf('Refinery29\Piston\Router\Routes\Route');

        $this->addRoute($route);
    }

    public function it_can_set_a_container(ContainerInterface $container)
    {
        $this->setContainer($container);
        $this->getContainer()->shouldReturn($container);
    }

    public function it_cannot_add_invalid_pre_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('addPreHook', [new \stdClass()]);
    }

    public function it_cannot_add_invalid_post_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('addPostHook', [new \stdClass()]);
    }

    public function it_can_add_pre_hooks()
    {
        $closure = function ($request, $response) {
            return $response;
        };

        $this->addPreHook($closure);

        $pre_hooks = $this->getPreHooks();

        $pre_hooks->shouldHaveType('Refinery29\Piston\Hooks\Queue');

        $pre_hooks->getNext()->shouldReturn($closure);
    }

    public function it_can_add_post_hooks()
    {
        $closure = function ($request, $response) {
            return $response;
        };

        $this->addPostHook($closure);

        $pre_hooks = $this->getPostHooks();

        $pre_hooks->shouldHaveType('Refinery29\Piston\Hooks\Queue');

        $pre_hooks->getNext()->shouldReturn($closure);
    }

    public function it_can_get_pre_hooks()
    {
        $this->getPreHooks()->shouldHaveType('Refinery29\Piston\Hooks\Queue');
    }

    public function it_can_get_post_hooks()
    {
        $this->getPostHooks()->shouldHaveType('Refinery29\Piston\Hooks\Queue');
    }

    public function it_can_set_a_request(Request $request)
    {
        $request->beADoubleOf('Refinery29\Piston\Request\Request');

        $this->setRequest($request);

        $this->getRequest()->shouldReturn($request);
    }

    public function it_creates_a_request_if_one_is_not_provided()
    {
        $this->getRequest()->shouldHaveType('Refinery29\Piston\Request\Request');
    }

    public function it_can_add_service_providers(ServiceProvider $provider)
    {
        $provider->beADoubleOf('League\Container\ServiceProvider');

        $this->register($provider);
    }

    public function it_can_redirect()
    {
        $this->redirect('123/something')->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    public function it_can_404()
    {
        $response = $this->notFound();
        $response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
        $response->getStatusCode()->shouldReturn(404);
    }
}
