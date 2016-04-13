<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston;

use Zend\Diactoros\ServerRequest;

/**
 * Class Request
 */
class Request extends ServerRequest
{
    const OFFSET_LIMIT_PAGINATION = 'offset_limit';
    const CURSOR_PAGINATION = 'cursor';

    /**
     * @var null
     */
    protected $paginationCursor;

    /**
     * @var string
     */
    protected $paginationType;

    /**
     * @var array
     */
    protected $requestedFields;

    /**
     * @var array
     */
    protected $includedResources;

    /**
     * @var string
     */
    protected $beforeCursor;

    /**
     * @var string
     */
    protected $afterCursor;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var CookieJar
     */
    private $cookieJar;

    /**
     * @var array
     */
    private $sorts = [];

    /**
     * @param CookieJar $jar
     * @param array     $serverParams
     * @param array     $uploadedFiles
     * @param string    $uri
     * @param string    $method
     * @param string    $body
     * @param array     $headers
     */
    public function __construct(
        CookieJar $jar = null,
        array $serverParams = [],
        array $uploadedFiles = [],
        $uri = null,
        $method = null,
        $body = 'php://input',
        array $headers = []
    ) {
        parent::__construct($serverParams, $uploadedFiles, $uri, $method, $body, $headers);

        $this->cookieJar = $jar ?: new CookieJar();
    }

    /**
     * @return array
     */
    public function getPaginationCursor()
    {
        return $this->beforeCursor
            ? ['before' => $this->beforeCursor]
            : ['after' => $this->afterCursor];
    }

    /**
     * @return array
     */
    public function getRequestedFields()
    {
        return $this->requestedFields;
    }

    /**
     * @param array $requestedFields
     *
     * @deprecated
     */
    public function setRequestedFields(array $requestedFields = [])
    {
        $this->requestedFields = $requestedFields;
    }

    /**
     * @return array
     */
    public function getIncludedResources()
    {
        return $this->includedResources;
    }

    /**
     * @param array $includedResources
     *
     * @deprecated
     */
    public function setIncludedResources(array $includedResources)
    {
        $this->includedResources = $includedResources;
    }

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return $this->paginationType !== null;
    }

    /**
     * @param string $afterCursor
     *
     * @deprecated
     */
    public function setAfterCursor($afterCursor)
    {
        $this->afterCursor = $afterCursor;
        $this->paginationType = self::CURSOR_PAGINATION;
    }

    /**
     * @param string $beforeCursor
     *
     * @deprecated
     */
    public function setBeforeCursor($beforeCursor)
    {
        $this->beforeCursor = $beforeCursor;
        $this->paginationType = self::CURSOR_PAGINATION;
    }

    /**
     * @param string $offset
     * @param string $limit
     *
     * @deprecated
     */
    public function setOffsetLimit($offset, $limit)
    {
        $this->offset = $offset;
        $this->limit = $limit;
        $this->paginationType = self::OFFSET_LIMIT_PAGINATION;
    }

    /**
     * @return array
     */
    public function getOffsetLimit()
    {
        if ($this->offset || $this->limit) {
            return ['offset' => $this->offset, 'limit' => $this->limit];
        }

        return [];
    }

    /**
     * @return string
     */
    public function getPaginationType()
    {
        return $this->paginationType;
    }

    /**
     * @param string $key
     * @param mixed  $val
     *
     * @return ServerRequest
     */
    public function withCookie($key, $val)
    {
        $this->cookieJar->set($key, $val);

        return $this->withCookieParams($this->cookieJar->all());
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getCookie($key)
    {
        return $this->cookieJar->get($key);
    }

    /**
     * @return array[string]mixed
     */
    public function getCookies()
    {
        return $this->cookieJar->all();
    }

    /**
     * @param string $key
     *
     * @return ServerRequest
     */
    public function clearCookie($key)
    {
        $this->cookieJar->clear($key);

        return $this->withCookieParams($this->cookieJar->all());
    }

    /**
     * @return ServerRequest
     */
    public function clearCookies()
    {
        $this->cookieJar->clearAll();

        return $this->withCookieParams($this->cookieJar->all());
    }

    /**
     * @param array $sorts
     */
    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    /**
     * @return array
     */
    public function getSorts()
    {
        return $this->sorts;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasSort($name)
    {
        return array_key_exists($name, $this->sorts);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getSort($name)
    {
        if (!$this->hasSort($name)) {
            return;
        }

        return $this->sorts[$name];
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return Request
     */
    public function withOffsetLimit($offset, $limit)
    {
        $new = clone $this;
        $new->offset = $offset;
        $new->limit = $limit;
        $new->paginationType = self::OFFSET_LIMIT_PAGINATION;

        return $new;
    }

    /**
     * @param string $beforeCursor
     *
     * @return Request
     */
    public function withBeforeCursor($beforeCursor)
    {
        $new = clone $this;
        $new->beforeCursor = $beforeCursor;
        $new->paginationType = self::CURSOR_PAGINATION;

        return $new;
    }

    /**
     * @return string
     */
    public function getBeforeCursor()
    {
        return $this->beforeCursor;
    }

    /**
     * @param string $afterCursor
     *
     * @return Request
     */
    public function withAfterCursor($afterCursor)
    {
        $new = clone $this;
        $new->afterCursor = $afterCursor;
        $new->paginationType = self::CURSOR_PAGINATION;

        return $new;
    }

    /**
     * @return string
     */
    public function getAfterCursor()
    {
        return $this->afterCursor;
    }

    /**
     * @param array $includedResources
     *
     * @return Request
     */
    public function withIncludedResources($includedResources)
    {
        $new = clone $this;
        $new->includedResources = $includedResources;

        return $new;
    }

    /**
     * @param array $requestedFields
     *
     * @return Request
     */
    public function withRequestedFields($requestedFields)
    {
        $new = clone $this;
        $new->requestedFields = $requestedFields;

        return $new;
    }
}
