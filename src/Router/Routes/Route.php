<?php namespace Refinery29\Piston\Router\Routes;

use Refinery29\Piston\Hooks\Hookable;

class Route
{
    use Hookable;

    /**
     * @var
     */
    private $verb;

    /**
     * @var
     */
    private $alias;

    /**
     * @var
     */
    private $action;

    /**
     * @var bool
     */
    private $is_paginated = false;

    /**
     * @var array
     */
    private $acceptable_verbs = ['POST', 'PUT', 'DELETE', 'GET'];

    /**
     * @param $verb
     * @param $alias
     * @param $action
     * @param bool $is_paginated
     */
    public function __construct($verb, $alias, $action, $is_paginated = false)
    {
        $this->validateVerb($verb);
        $this->action = $action;
        $this->is_paginated = $is_paginated;
        $this->alias = $alias;
    }

    /**
     * @param $verb
     */
    private function validateVerb($verb)
    {
        if (!in_array($verb, $this->acceptable_verbs)) {
            throw new \InvalidArgumentException('Invalid Route Verb Supplied.');
        }

        $this->verb = $verb;
    }

    /**
     * @param $alias
     * @param $action
     * @param bool $is_paginated
     * @return static
     */
    public static function get($alias, $action, $is_paginated = false)
    {
        return new static('GET', $alias, $action, $is_paginated);
    }

    /**
     * @param $alias
     * @param $action
     * @return static
     */
    public static function post($alias, $action)
    {
        return new static('POST', $alias, $action, false);
    }

    /**
     * @param $alias
     * @param $action
     * @return static
     */
    public static function delete($alias, $action)
    {
        return new static('DELETE', $alias, $action, false);
    }

    /**
     * @param $alias
     * @param $action
     * @return static
     */
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

    /**
     * @return bool
     */
    public function isPaginated()
    {
        return $this->is_paginated;
    }
}
