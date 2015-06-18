<?php

namespace spec\Refinery29\Piston\Router;

use League\Container\Container;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Refinery29\Piston\Stubs\FooController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PistonStrategySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(new Container());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Router\PistonStrategy');
    }

    public function it_must_return_a_response()
    {
        $this->dispatch(function ($req, $resp) {
            return $resp;
        }, [])->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }

    public function it_throws_exceptions_on_invalid_response()
    {
        $this->shouldThrow('\Exception')->duringValidateResponse('YOLO');
    }

    public function it_can_resolve_injected_controller(FooController $controller)
    {
        $this->resolveController([$controller, 'fooAction'])->shouldReturn([$controller, 'fooAction']);
    }

    public function it_can_resolve_string_controller(FooController $controller)
    {
        $controller = $this->resolveController(['Refinery29\Piston\Stubs\FooController', 'fooAction'])[0];
        $controller->shouldHaveType('Refinery29\Piston\Stubs\FooController');
    }

    public function it_can_dispatch_controller(Request $request, Response $response)
    {
        $response->beADoubleOf('Symfony\Component\HttpFoundation\Response');
        $request->beADoubleOf('Symfony\Component\HttpFoundation\Request');

        $controller_response = $this->invokeAction(['Refinery29\Piston\Stubs\FooController', 'fooAction'], [$request, $response]);
        $controller_response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
