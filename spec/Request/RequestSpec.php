<?php

namespace spec\Refinery29\Piston\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Refinery29\Piston\Request\Request');
    }

    public function it_can_set_requested_fields()
    {
        $req_fields = ['yolo', 'gogo'];
        $this->setRequestedFields($req_fields);

        $this->getRequestedFields()->shouldReturn($req_fields);
        $this->requestsSpecificFields()->shouldReturn(true);
    }

    public function it_can_set_included_resources()
    {
        $included_resources = ['monica', 'chandler'];
        $this->setIncludedResources($included_resources);

        $this->getIncludedResources()->shouldReturn($included_resources);
        $this->hasIncludedResources()->shouldReturn(true);
    }

    public function it_can_set_pagination()
    {
        $pagination_cursor = rand();
        $this->setPaginationCursor($pagination_cursor);

        $this->getPaginationCursor()->shouldReturn($pagination_cursor);
        $this->isPaginated()->shouldReturn(true);
    }
}
