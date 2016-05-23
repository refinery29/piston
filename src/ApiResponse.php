<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston;

use Psr\Http\Message\StreamInterface;
use Refinery29\ApiOutput\Resource\Error\ErrorCollection;
use Refinery29\ApiOutput\Resource\Pagination\Pagination;
use Refinery29\ApiOutput\Resource\Result;
use Refinery29\ApiOutput\ResponseBody;
use Zend\Diactoros\Response as DiactorosResponse;

class ApiResponse extends DiactorosResponse implements CompiledResponse
{
    /**
     * @var ResponseBody
     */
    private $responseBody;

    /**
     * @var bool
     */
    private $isTopless = false;

    /**
     * @param ResponseBody                    $responseBody
     * @param string|resource|StreamInterface $body         Stream identifier and/or actual stream resource
     */
    public function __construct(ResponseBody $responseBody = null, $body = 'php://memory')
    {
        parent::__construct($body, 200, ['content-type' => 'application/json']);
        $this->responseBody = $responseBody ?: new ResponseBody();
    }

    /**
     * @param Pagination $pagination
     */
    public function setPagination(Pagination $pagination)
    {
        $this->checksForToplevelObject();
        $this->responseBody->addMember($pagination->getSerializer());
    }

    /**
     * @param ErrorCollection $error
     */
    public function setErrors(ErrorCollection $error)
    {
        $this->checksForToplevelObject();
        $this->responseBody->addMember($error->getSerializer());
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result)
    {
        $this->checksForToplevelObject();
        $this->responseBody->addMember($result->getSerializer());
    }

    /**
     * @param ToplessResult $toplessResult
     * @throws \Exception
     */
    public function setToplessResult(ToplessResult $toplessResult)
    {
        if (count($this->responseBody->getMembers()) > 0){
            throw new \Exception('Topless Result cannot be used with other Resources');
        }
        $this->responseBody->addMember($toplessResult->getSerializer());
        $this->isTopless = true;
    }

    /**
     * @throws \Exception
     */
    private function checksForToplevelObject()
    {
        if ($this->isTopless) {
            throw new \Exception('Cannot add other members when making a Topless Response');
        }
    }

    /**
     * @param int $code
     *
     * @return static
     */
    public function setStatusCode($code)
    {
        return $this->withStatus($code);
    }

    /**
     * @return StreamInterface
     */
    public function compileContent()
    {
        $output = $this->responseBody->getOutput();

        $this->getBody()->write($output);

        return $this->getBody();
    }
}
