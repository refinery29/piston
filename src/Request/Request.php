<?php

namespace Refinery29\Piston\Request;

use Symfony\Component\HttpFoundation\Request as SRequest;

class Request extends SRequest
{
    protected $pagination_cursor = null;
    protected $requested_fields = null;
    protected $included_resources = null;

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

    public function isPaginated()
    {
        return !is_null($this->pagination_cursor);
    }

    public function hasIncludedResources()
    {
        return !is_null($this->included_resources);
    }

    public function requestsSpecificFields()
    {
        return !is_null($this->requested_fields);
    }
}
