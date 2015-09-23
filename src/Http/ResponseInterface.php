<?php

namespace Refinery29\Piston\Http;

use Refinery29\ApiOutput\Resource\Error\ErrorCollection;
use Refinery29\ApiOutput\Resource\Pagination\Pagination;
use Refinery29\ApiOutput\Resource\Result;
use Refinery29\ApiOutput\ResponseBody;

interface ResponseInterface
{
    /**
     * @param ResponseBody $responseBody
     */
    public function setResponseBody(ResponseBody $responseBody);

    /**
     * @param Pagination $pagination
     */
    public function setPagination(Pagination $pagination);

    /**
     * @param ErrorCollection $error
     */
    public function setErrors(ErrorCollection $error);

    /**
     * @param Result $result
     */
    public function setResult(Result $result);

    /**
     * @param int $code
     */
    public function setStatusCode($code);

    /**
     * @return mixed
     */
    public function send();
}