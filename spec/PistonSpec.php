<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston;

use League\Container\Container;
use League\Container\ServiceProvider;
use League\Pipeline\CallableStage;
use League\Pipeline\Pipeline;
use League\Pipeline\StageInterface;
use League\Route\Http\Exception\NotFoundException;
use PhpSpec\ObjectBehavior;
use Refinery29\Piston\ApiResponse;
use Refinery29\Piston\Piston;
use Refinery29\Piston\Request;
use Refinery29\Piston\RequestFactory;
use Refinery29\Piston\Router\RouteGroup;
use spec\Refinery29\Piston\Stub\FooController;
use spec\Refinery29\Piston\Stub\PassThroughEmitter;
use spec\Refinery29\Piston\Stub\StringEmitter;
use spec\Refinery29\Piston\Stub\TestException;
use Zend\Diactoros\Uri;

/**
 * @mixin Piston
 */
class PistonSpec extends ObjectBehavior
{
    public function let(Container $container)
    {
        $container->beADoubleOf(Container::class);
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Piston::class);
    }

    public function it_can_add_a_route_group()
    {
        $this->shouldHaveType(Piston::class);
        $this->group('prefix', function () {
        })->shouldHaveType(RouteGroup::class);
    }

    public function it_pre_processes_a_request_with_included_resources()
    {
        /** @var Request $request */
        $request = RequestFactory::fromGlobals()
            ->withUri(new Uri('/something'))
            ->withQueryParams(['include' => 'something']);

        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->get('/something', FooController::class . '::test');

        $this->launch();

        $this->getRequest()->getIncludedResources()->shouldContain('something');
    }

    public function it_pre_process_a_request_with_requested_fields()
    {
        /** @var Request $request */
        $request = RequestFactory::fromGlobals()
            ->withUri(new Uri('/something'))
            ->withQueryParams(['fields' => 'something']);

        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->get('/something', FooController::class . '::test');

        $this->launch();

        $this->getRequest()->getRequestedFields()->shouldContain('something');
    }

    public function it_pre_processes_a_request_with_cursor_based_pagination()
    {
        /** @var Request $request */
        $request = RequestFactory::fromGlobals()
            ->withUri(new Uri('/something'))
            ->withQueryParams(['before' => 'something']);

        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->get('/something', FooController::class . '::test'); // also still have convenience http methods (get, post, put etc

        $this->launch();

        $this->getRequest()->getPaginationCursor()->shouldContain('something');
    }

    public function it_pre_processes_a_request_with_ol_based_pagination()
    {
        /** @var Request $request */
        $request = RequestFactory::fromGlobals()
            ->withUri(new Uri('/something'))
            ->withQueryParams(['offset' => 10]);

        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->get('/something', FooController::class . '::test'); // also still have convenience http methods (get, post, put etc

        $this->launch();

        $this->getRequest()->getOffsetLimit()->shouldReturn(['offset' => 10, 'limit' => 10]);
    }

    public function it_can_add_middleware(StageInterface $operation)
    {
        $this->addMiddleware($operation);
        $this->getPipeline()->shouldHaveType(Pipeline::class);
    }

    public function it_can_add_service_providers(ServiceProvider\AbstractServiceProvider $provider)
    {
        $provider->beADoubleOf(ServiceProvider\AbstractServiceProvider::class);

        $this->register($provider);
    }

    public function it_launches_with_expected_result()
    {
        $request = RequestFactory::fromGlobals()->withUri(new Uri('/prefix/something'));
        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->group('/prefix', function (RouteGroup $router) {
            $router->get('/something', FooController::class . '::test')->setName('something');
        });

        $this->launch()->shouldReturn('{"result":{"something":"yolo"}}');
    }

    public function it_can_launch_non_compiled_responses()
    {
        $request = RequestFactory::fromGlobals()->withUri(new Uri('/prefix/something'));
        $emitter = new StringEmitter();

        $this->beConstructedWith(null, $request, $emitter);
        $this->group('/prefix', function (RouteGroup $router) {
            $router->get('/something', FooController::class . '::testHTMLResponse')->setName('something');
        });

        $this->launch()->shouldReturn('<p>Hello World</p>');
    }

    public function it_can_register_and_catch_exceptions()
    {
        $this->addMiddleware(CallableStage::forCallable(function () {
            throw new TestException();
        }));

        $this->registerException(TestException::class, function () {
            return 'How now, Brown Cow?';
        });

        $this->launch()->shouldReturn('How now, Brown Cow?');
    }

    public function it_returns_404_when_not_found()
    {
        $emitter = new PassThroughEmitter();
        $this->beConstructedWith(null, null, $emitter);
        $response = $this->launch();
        $response->shouldHaveType(ApiResponse::class);

        $response->getStatusCode()->shouldReturn(404);
    }

    public function it_returns_correct_error_code()
    {
        $emitter = new PassThroughEmitter();
        $this->beConstructedWith(null, null, $emitter);

        $this->addMiddleware(CallableStage::forCallable(function () {
            throw new TestException();
        }));

        $this->registerException(TestException::class, function () {
            return $this->getErrorResponse(300);
        });

        $response = $this->launch()->getWrappedObject();
        $response->shouldHaveType(ApiResponse::class);

        $response->getStatusCode()->shouldReturn(300);
    }

    public function it_can_return_error_response_with_custom_body()
    {
        $emitter = new PassThroughEmitter();
        $this->beConstructedWith(null, null, $emitter);

        $this->addMiddleware(CallableStage::forCallable(function () {
            throw new TestException();
        }));

        $this->registerException(TestException::class, function () {
            return $this->getErrorResponse(300, "I'm blue da ba dee");
        });

        $response = $this->launch()->getWrappedObject();
        $response->shouldHaveType(ApiResponse::class);

        $response->getBody()->__toString()->shouldReturn("I'm blue da ba dee");
    }

    public function it_rethrows_uncaught_exceptions()
    {
        $this->addMiddleware(CallableStage::forCallable(function () {
            throw new TestException();
        }));

        $this->shouldThrow(TestException::class)->duringLaunch();
    }

    public function it_can_override_already_registered_exceptions()
    {
        $emitter = new PassThroughEmitter();
        $this->beConstructedWith(null, null, $emitter);
        $response = $this->launch();
        $response->shouldHaveType(ApiResponse::class);

        $response->getStatusCode()->shouldReturn(404);

        $this->addMiddleware(CallableStage::forCallable(function () {
            throw new NotFoundException();
        }));

        $this->registerException(NotFoundException::class, function () {
            return 'How now, Brown Cow?';
        });

        $this->launch()->shouldReturn('How now, Brown Cow?');
    }
}
