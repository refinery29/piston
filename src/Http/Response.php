<?php

namespace Refinery29\Piston\Http;

use Refinery29\ApiOutput\Resource\Error\ErrorCollection;
use Refinery29\ApiOutput\Resource\Pagination\Pagination;
use Refinery29\ApiOutput\Resource\Result;
use Refinery29\ApiOutput\ResponseBody;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response implements ResponseInterface
{
    /**
     * @var ResponseBody
     */
    private $responseBody;
    /**
     * @var SymfonyResponse
     */
    private $response;

    /**
     * @param SymfonyResponse $response
     * @param ResponseBody    $responseBody
     */
    public function __construct(SymfonyResponse $response, ResponseBody $responseBody = null)
    {
        $this->responseBody = $responseBody ?: new ResponseBody();

        $this->response = $response;
        $this->response->headers->set('Content-Type', 'application/json');
    }

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

    public function setResult(Result $result)
    {
        $this->responseBody->addMember($result->getSerializer());
    }

    /**
     * @param int $code
     */
    public function setStatusCode($code)
    {
        $this->response->setStatusCode($code);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function send()
    {
        $this->setResponseContent();
        $this->response->send();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $this->setResponseContent();

        return $this->response->getContent();
    }

    private function setResponseContent()
    {
        $output = $this->responseBody->getOutput();
        $this->response->setContent($output);
    }
}
