<?php namespace Refinery29\Piston\Stubs;

use Refinery29\Piston\Router\Routeable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: kayla.daniels
 * Date: 6/9/15
 * Time: 4:39 PM
 */
class FooController implements Routeable
{
    public function fooAction(Request $req, Response $resp)
    {
        return $resp;
    }
}
