<?php

namespace Refinery29\Piston\Routes;

use Refinery29\Piston\Hooks\Hookable;

class Route
{
    use Hookable;

    protected $verb;
    protected $alias;
    protected $action;
    protected $is_paginated = false;

    private $acceptable_verbs = ['POST', 'PUT', 'DELETE', 'GET'];

    public function __construct($verb, $alias, $action, $is_paginated = false)
    {
        $this->validateVerb($verb);
        $this->is_paginated = $is_paginated;
        $this->alias = $alias;
        $this->action = $action;
    }

    private function validateVerb($verb)
    {
        if (!in_array($verb, $this->acceptable_verbs)) {
            throw new \InvalidArgumentException('Invalid Route Verb Supplied.');
        }

        $this->verb = $verb;
    }

    public static function get($alias, $action, $is_paginated = false)
    {
        return new static('GET', $alias, $action, $is_paginated);
    }

    public static function post($alias, $action)
    {
        return new static('POST', $alias, $action, false);
    }

    public static function delete($alias, $action)
    {
        return new static('DELETE', $alias, $action, false);
    }

    public static function put($alias, $action)
    {
        return new static('PUT', $alias, $action, false);
    }

    /**
     * @return mixed
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    public function isPaginated()
    {
        return $this->is_paginated;
    }
}
