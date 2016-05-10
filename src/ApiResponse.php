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
        $this->responseBody->addMember($pagination->getSerializer());
    }

    /**
     * @param ErrorCollection $error
     */
    public function setErrors(ErrorCollection $error)
    {
        $this->responseBody->addMember($error->getSerializer());
    }

    /**
     * @param Result $result
     */
    public function setResult(Result $result)
    {
        $this->responseBody->addMember($result->getSerializer());
    }

    /**
     * @param int $code
     *
     * @deprecated
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
