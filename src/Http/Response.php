<?php namespace Refinery29\Piston\Http;

use Symfony\Component\HttpFoundation\Response as SResponse;

class Response extends SResponse
{
    private $pagination;

    public function setPaginationCursors(array $cursor)
    {
        $this->pagination = $cursor;
    }

    public function getPaginationCursors()
    {
        return $this->pagination;
    }
}
