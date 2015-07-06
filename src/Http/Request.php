<?php namespace Refinery29\Piston\Http;

use Symfony\Component\HttpFoundation\Request as SRequest;

/**
 * Class Request
 * @package Refinery29\Piston\Request
 */
class Request extends SRequest
{
    /**
     * @var null
     */
    private $pagination_cursor = null;

    /**
     * @var null
     */
    private $requested_fields = null;

    /**
     * @var null
     */
    private $included_resources = null;

    /**
     * @return null
     */
    public function getPaginationCursor()
    {
        return $this->pagination_cursor;
    }

    /**
     * @param null $pagination_cursor
     */
    public function setPaginationCursor($pagination_cursor)
    {
        $this->pagination_cursor = $pagination_cursor;
    }

    /**
     * @return null
     */
    public function getRequestedFields()
    {
        return $this->requested_fields;
    }

    /**
     * @param null $requested_fields
     */
    public function setRequestedFields($requested_fields)
    {
        $this->requested_fields = $requested_fields;
    }

    /**
     * @return null
     */
    public function getIncludedResources()
    {
        return $this->included_resources;
    }

    /**
     * @param null $included_resources
     */
    public function setIncludedResources($included_resources)
    {
        $this->included_resources = $included_resources;
    }

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return !empty($this->pagination_cursor);
    }

    /**
     * @return bool
     */
    public function hasIncludedResources()
    {
        return !is_null($this->included_resources);
    }

    /**
     * @return bool
     */
    public function hasRequestedFields()
    {
        return !is_null($this->requested_fields);
    }
}
