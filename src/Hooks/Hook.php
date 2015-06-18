<?php namespace Refinery29\Piston\Hooks;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface Hook
{
    /**
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function apply(Request $request, Response $response);
}
