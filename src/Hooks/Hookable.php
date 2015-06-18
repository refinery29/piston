<?php

namespace Refinery29\Piston\Hooks;

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

    public function addPreHook($hook)
    {
        $this->bootstrapHooks();
        $this->validateHook($hook);

        $this->pre_hooks->addHook($hook);

        return $this;
    }

    protected function bootstrapHooks()
    {
        if ($this->pre_hooks == null) {
            $this->pre_hooks = new Queue();
        }

        if ($this->post_hooks == null) {
            $this->post_hooks = new Queue();
        }
    }

    private function validateHook($hook)
    {
        if (!($hook instanceof \Closure) && !($hook instanceof Hook)) {
            throw new InvalidArgumentException('You may only use closures and Refinery29/Piston/Hooks/Hook as a Hook');
        }
    }

    public function addPostHook($hook)
    {
        $this->bootstrapHooks();
        $this->validateHook($hook);

        $this->post_hooks->addHook($hook);

        return $this;
    }

    public function getPreHooks()
    {
        $this->bootstrapHooks();

        return $this->pre_hooks;
    }

    public function getPostHooks()
    {
        $this->bootstrapHooks();

        return $this->post_hooks;
    }
}
