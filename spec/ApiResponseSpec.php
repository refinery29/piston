<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace spec\Refinery29\Piston;

use PhpSpec\ObjectBehavior;
use Refinery29\ApiOutput\Resource\CustomResult;
use Refinery29\ApiOutput\Resource\Error\Error;
use Refinery29\ApiOutput\Resource\Error\ErrorCollection;
use Refinery29\ApiOutput\Resource\Error\ErrorCollection as ErrorCollectionResource;
use Refinery29\ApiOutput\Resource\Pagination\Pagination;
use Refinery29\ApiOutput\Resource\Result;
use Refinery29\ApiOutput\ResponseBody;
use Refinery29\Piston\ApiResponse;
use Zend\Diactoros\Stream;

/**
 * @mixin ApiResponse
 */
class ApiResponseSpec extends ObjectBehavior
{
    public function it_can_be_constructed_without_response_body(Stream $stream)
    {
        $this->beConstructedWith(null, $stream);

        $this->compileContent();
        $this->getBody()->shouldReturn($stream);

        $stream->write('{}')->shouldBeCalled(true);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ApiResponse::class);
    }

    public function it_can_set_status_code()
    {
        $this->setStatusCode(200)->shouldHaveType(ApiResponse::class);
        $this->setStatusCode(400)->getStatusCode()->shouldReturn(400);
    }

    public function it_can_set_errors(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        $ec = new ErrorCollectionResource([new Error('type', 'code')]);

        $this->setErrors($ec);

        $rb->addMember($ec->getSerializer())->shouldBeCalled();
    }

    public function it_can_set_result(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        $result = new Result(['yes' => 'no']);

        $this->setResult($result);

        $rb->addMember($result->getSerializer())->shouldBeCalled();
    }

    public function it_can_set_custom_result(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        $customResult = new CustomResult(['yes' => 'no']);

        $this->setCustomResult($customResult);

        $rb->addMember($customResult->getSerializer())->shouldBeCalled();
    }

    public function it_cannot_set_error_pagination_after_setting_custom_result(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        // Assign Custom
        $customResult = new CustomResult(['yes' => 'no']);

        $this->setCustomResult($customResult);

        $rb->addMember($customResult->getSerializer())->shouldBeCalled();

        // Assign Pagination

        $result = new Pagination();

        $this->shouldThrow('\Exception')->duringSetPagination($result);

        $result = new ErrorCollection();

        $this->shouldThrow('\Exception')->duringSetResult($result);

        $result = new CustomResult(['yes' => 'no']);

        $this->shouldThrow('\Exception')->duringSetCustomResult($result);
    }

    public function it_can_set_pagination(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        $result = new Pagination();

        $this->setPagination($result);

        $rb->addMember($result->getSerializer())->shouldBeCalled();
    }

    public function it_has_json_header(ResponseBody $rb)
    {
        $this->beConstructedWith($rb);

        $this->getHeaderLine('content-type')->shouldReturn('application/json');
    }
}
