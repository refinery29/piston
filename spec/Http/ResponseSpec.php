<?php

namespace spec\Refinery29\Piston\Http;

use PhpSpec\ObjectBehavior;
use Refinery29\ApiOutput\ResponseBody;
use Refinery29\Piston\Http\Response;

class ResponseSpec extends ObjectBehavior
{
    public function let(ResponseBody $body)
    {
        $this->beConstructedWith(\Symfony\Component\HttpFoundation\Response::create(), $body);
    }

    public function it_can_be_constructed_without_response_body()
    {
        $this->beConstructedWith(\Symfony\Component\HttpFoundation\Response::create());

        $this->getContent()->shouldReturn('{}');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Response::class);
    }

    public function it_can_set_and_get_status_code()
    {
        $this->setStatusCode(202);

        $this->getStatusCode()->shouldReturn(202);
    }
}
