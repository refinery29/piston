<?php

namespace spec\Refinery29\Piston\Hooks;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Refinery29\Piston\Hooks\Hook;
use Refinery29\Piston\Hooks\Queue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkerSpec extends ObjectBehavior
{
    public function it_can_work_a_queue(Queue $queue, Request $req, Response $resp, Hook $hook)
    {
        $hook->apply($req, $resp)->willReturn($resp);

        $queue->beADoubleOf('Refinery29\Piston\Hooks\Queue');
        $queue->getHooks()->willReturn([$hook]);

        $this::work($queue, $req, $resp)->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
