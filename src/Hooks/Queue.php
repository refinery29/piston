<?php namespace Refinery29\Piston\Hooks;

use Closure;
use InvalidArgumentException;

class Queue
{
    /**
     * @var Hook[]
     */
    private $hooks = [];

    /**
     * @param \Closure|Hook
     * @return $this
     */
    public function addHook($hook)
    {
        $this->validateHook($hook);

        $this->hooks[] = $hook;

        return $this;
    }

    /**
     * @return \Closure|Hook
     */
    public function getNext()
    {
        return array_shift($this->hooks);
    }

    /**
     * @param \Closure|Hook
     * @param $position
     * @return $this
     */
    public function insertHook($hook, $position)
    {
        $this->validateHook($hook);
        array_splice($this->hooks, $position, 0, $hook);

        return $this;
    }

    /**
     * @param $hook
     * @throws InvalidArgumentException
     */
    private function validateHook($hook)
    {
        if (!($hook instanceof Closure) && !($hook instanceof Hook)) {
            throw new InvalidArgumentException('You may only use closures and Refinery29/Piston/Hooks/Hook as a Hook');
        }
    }

    /**
     * @return Hook[]
     */
    public function getHooks()
    {
        return $this->hooks;
    }
}
