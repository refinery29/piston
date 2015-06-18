<?php namespace Refinery29\Piston\Request;

use Symfony\Component\HttpFoundation\Request as SRequest;

class Request extends SRequest
{
    /**
     * @var string
     */
    protected $pagination_cursor = null;

    /**
     * @var array
     */
    protected $requested_fields = null;

    /**
     * @var array
     */
    protected $included_resources = null;

    /**
     * @return string
     */
    public function getPaginationCursor()
    {
        return $this->pagination_cursor;
    }

    /**
     * @return array
     */
    public function getRequestedFields()
    {
        return $this->requested_fields;
    }

    /**
     * @return array
     */
    public function getIncludedResources()
    {
        return $this->included_resources;
    }

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return !is_null($this->pagination_cursor);
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
    public function requestsSpecificFields()
    {
        return !is_null($this->requested_fields);
    }

    /**
     * @param array $requested_fields
     */
    public function setRequestedFields($requested_fields)
    {
        $this->requested_fields = $requested_fields;
    }

    /**
     * @param string $pagination_cursor
     */
    public function setPaginationCursor($pagination_cursor)
    {
        $this->pagination_cursor = $pagination_cursor;
    }

    /**
     * @param array $included_resources
     */
    public function setIncludedResources($included_resources)
    {
        $this->included_resources = $included_resources;
    }
}