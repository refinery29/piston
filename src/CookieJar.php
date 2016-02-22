<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston;

use Assert\Assertion;

class CookieJar
{
    /**
     * @var array
     */
    private $cookies;

    /**
     * @param array $cookies
     */
    public function __construct(array $cookies = [])
    {
        Assertion::allString(array_keys($cookies), 'CookieJar must be instantiated with an associative array');

        $this->cookies = $cookies;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        Assertion::string($key);

        if (!array_key_exists($key, $this->cookies)) {
            return;
        }

        return $this->cookies[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        Assertion::string($key);

        $this->cookies[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function clear($key)
    {
        Assertion::string($key);

        unset($this->cookies[$key]);
    }

    public function clearAll()
    {
        $this->cookies = [];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->cookies;
    }
}
