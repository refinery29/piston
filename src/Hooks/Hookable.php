<?php

namespace Refinery29\Piston\Hooks;

use Closure;
use InvalidArgumentException;

trait Hookable
{
    /**
     * @var Queue
     */
    protected $pre_hooks = null;

    /**
     * @var Queue
     */
    protected $post_hooks = null;

    /**
     * @param Closure|Hook $hook
     *
     * @return $this
     */
    public function addPreHook($hook)
    {
        $this->bootstrapHooks();
        $this->validateHook($hook);

        $this->pre_hooks->addHook($hook);

        return $this;
    }

    /**
     * @param Closure|Hook $hook
     *
     * @return $this
     */
    public function addPostHook($hook)
    {
        $this->bootstrapHooks();
        $this->validateHook($hook);

        $this->post_hooks->addHook($hook);

        return $this;
    }

    /**
     * @param mixed $hook
     */
    private function validateHook($hook)
    {
        if (!($hook instanceof Closure) && !($hook instanceof Hook)) {
            throw new InvalidArgumentException('You may only use closures and Refinery29/Piston/Hooks/Hook as a Hook');
        }
    }

    /**
     * @return Queue
     */
    public function getPreHooks()
    {
        $this->bootstrapHooks();
        return $this->pre_hooks;
    }

    /**
     * @return Queue
     */
    public function getPostHooks()
    {
        $this->bootstrapHooks();
        return $this->post_hooks;
    }

    protected function bootstrapHooks()
    {
        if ($this->pre_hooks == NULL){
            $this->pre_hooks = new Queue();
        }

        if ($this->post_hooks == NULL){
            $this->post_hooks = new Queue();
        }
    }
}