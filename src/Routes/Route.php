<?php namespace Refinery29\Piston\Routes;

use InvalidArgumentException;
use Refinery29\Piston\Hooks\Hookable;

class Route
{
    use Hookable;

    /**
     * @var string
     */
    protected $verb;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var bool
     */
    protected $is_paginated = false;

    /**
     * @var array
     */
    private $acceptable_verbs = ['POST', 'PUT', 'DELETE', 'GET'];

    /**
     * @param string $verb
     * @param string $alias
     * @param string $action
     * @param bool   $is_paginated
     */
    public function __construct($verb, $alias, $action, $is_paginated = false)
    {
        $this->validateVerb($verb);
        $this->is_paginated = $is_paginated;
        $this->alias = $alias;
        $this->action = $action;
    }

    /**
     * @param string $alias
     * @param string $action
     * @param bool   $is_paginated
     *
     * @return static
     */
    static public function get($alias, $action, $is_paginated = false)
    {
        return new static('GET', $alias, $action, $is_paginated);
    }

    /**
     * @param string $alias
     * @param string $action
     *
     * @return static
     */
    static public function post($alias, $action)
    {
        return new static('POST', $alias, $action, false);
    }

    /**
     * @param string $alias
     * @param string $action
     *
     * @return static
     */
    static public function delete($alias, $action)
    {
        return new static('DELETE', $alias, $action, false);
    }

    /**
     * @param string $alias
     * @param string $action
     *
     * @return static
     */
    static public function put($alias, $action)
    {
        return new static('PUT', $alias, $action, false);
    }

    /**
     * @param string $verb
     *
     * @throws InvalidArgumentException
     */
    private function validateVerb($verb)
    {
        if (!in_array($verb, $this->acceptable_verbs)) {
            throw new InvalidArgumentException('Invalid Route Verb Supplied.');
        }

        $this->verb = $verb;
    }

    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->verb;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
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
