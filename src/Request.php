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
    /**
     * @param CookieJar $jar
     * @param array     $serverParams
     * @param array     $uploadedFiles
     * @param null      $uri
     * @param null      $method
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

    const OFFSET_LIMIT_PAGINATION = 'offset_limit';
    const CURSOR_PAGINATION = 'cursor';

    /**
     * @var null
     */
    protected $paginationCursor = null;

    /**
     * @var null
     */
    protected $paginationType = null;

    /**
     * @var null
     */
    protected $requestedFields = null;

    /**
     * @var null
     */
    protected $includedResources = null;

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
    private $sorts = null;

    /**
     * @return string
     */
    public function getPaginationCursor()
    {
        return $this->beforeCursor
            ? ['before' => $this->beforeCursor]
            : ['after' => $this->afterCursor];
    }

    /**
     */
    public function getRequestedFields()
    {
        return $this->requestedFields;
    }

    /**
     * @param null $requestedFields
     */
    public function setRequestedFields($requestedFields)
    {
        $this->requestedFields = $requestedFields;
    }

    /**
     */
    public function getIncludedResources()
    {
        return $this->includedResources;
    }

    /**
     * @param null $included_resources
     */
    public function setIncludedResources($included_resources)
    {
        $this->includedResources = $included_resources;
    }

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return $this->paginationType !== null;
    }

    /**
     * @param string $after_cursor
     */
    public function setAfterCursor($after_cursor)
    {
        $this->afterCursor = $after_cursor;
        $this->paginationType = self::CURSOR_PAGINATION;
    }

    /**
     * @param string $before_cursor
     */
    public function setBeforeCursor($before_cursor)
    {
        $this->beforeCursor = $before_cursor;
        $this->paginationType = self::CURSOR_PAGINATION;
    }

    /**
     * @param string $offset
     * @param string $limit
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

    /** @return string */
    public function getPaginationType()
    {
        return $this->paginationType;
    }

    /**
     * @param $key
     * @param $val
     *
     * @return Request
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
     * @return array
     */
    public function getCookies()
    {
        return $this->cookieJar->all();
    }

    public function clearCookie($key)
    {
        $this->cookieJar->clear($key);

        return $this->withCookieParams($this->cookieJar->all());
    }

    public function clearCookies()
    {
        $this->cookieJar->clearAll();

        return $this->withCookieParams($this->cookieJar->all());
    }

    /**
     * @param int $offset
     * @param int $limit
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
     * @param array $sorts
     */
    public function setSorts(array $sorts)
    {
        $this->sorts = $sorts;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasSort($name)
    {
        if ($this->sorts === null) {
            return false;
        }

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
}
