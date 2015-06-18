<?php

namespace spec\Refinery29\Piston\Hooks;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueueSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Hooks\Queue');
    }

    public function it_can_get_hooks()
    {
        $this->getHooks()->shouldReturn([]);
    }

    public function it_can_add_a_hook()
    {
        $closure = function ($request, $response) {
            echo "YES";
        };

        $this->addHook($closure);

        $this->getHooks()->shouldHaveCount(1);

        $this->getNext()->shouldReturn($closure);
    }

    public function it_is_a_fifo_queue()
    {
        $closure1 = function ($request, $response) {
            echo "YES";
        };
        $closure2 = function ($request, $response) {
            echo "YES";
        };
        $closure3 = function ($request, $response) {
            echo "YES";
        };

        $this->addHook($closure1);
        $this->addHook($closure2);
        $this->addHook($closure3);

        $this->getHooks()->shouldHaveCount(3);

        $this->getNext()->shouldReturn($closure1);
        $this->getNext()->shouldReturn($closure2);
        $this->getNext()->shouldReturn($closure3);
    }

    public function it_can_insert_a_hook()
    {
        $closure = function ($request, $response) {
            echo "YES";
        };

        $this->addHook(function ($request, $respo) {
        });
        $this->addHook(function ($request, $respo) {
        });

        $this->insertHook($closure, 0);

        $this->getHooks()->shouldHaveCount(3);

        $this->getNext()->shouldReturn($closure);
    }

    public function it_cannot_add_invalid_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('addHook', [new \stdClass()]);
    }

    public function it_cannot_insert_invalid_hook()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('insertHook', [new \stdClass(), 2]);
    }
}
