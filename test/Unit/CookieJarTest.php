<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Test\Unit;

use InvalidArgumentException;
use Refinery29\Piston\CookieJar;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class CookieJarTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function testConstructorRejectsInvalidCookies()
    {
        $this->expectException(InvalidArgumentException::class);

        $cookies = $this->getFaker()->words;

        new CookieJar($cookies);
    }

    public function testConstructorSetsCookies()
    {
        $cookies = $this->cookies();

        $cookieJar = new CookieJar($cookies);

        $this->assertSame($cookies, $cookieJar->all());
    }

    /**
     * @dataProvider providerInvalidKey
     *
     * @param mixed $key
     */
    public function testSetRejectsInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $value = $this->getFaker()->word;

        $cookieJar = new CookieJar();

        $cookieJar->set(
            $key,
            $value
        );
    }

    public function testCanSetValue()
    {
        $faker = $this->getFaker();

        $key = $faker->word;
        $value = $faker->word;

        $cookieJar = new CookieJar();

        $cookieJar->set($key, $value);

        $this->assertSame($value, $cookieJar->get($key));
    }

    /**
     * @dataProvider providerInvalidKey
     *
     * @param mixed $key
     */
    public function testGetRejectsInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $cookieJar = new CookieJar();

        $cookieJar->get($key);
    }

    public function testGetReturnsNullIfKeyNotFound()
    {
        $key = $this->getFaker()->word;

        $cookieJar = new CookieJar();

        $this->assertNull($cookieJar->get($key));
    }

    /**
     * @dataProvider providerInvalidKey
     *
     * @param mixed $key
     */
    public function testClearRejectsInvalidKey($key)
    {
        $this->expectException(InvalidArgumentException::class);

        $cookieJar = new CookieJar();

        $cookieJar->clear($key);
    }

    /**
     * @return \Generator
     */
    public function providerInvalidKey()
    {
        $faker = $this->getFaker();

        $values = [
            $faker->randomNumber(),
            $faker->randomFloat(),
            $faker->words(),
            null,
            false,
            true,
            new \stdClass(),
        ];

        foreach ($values as $value) {
            yield [
                $value,
            ];
        }
    }

    public function testCanClearCookie()
    {
        $cookies = $this->cookies();

        $key = \array_rand($cookies, 1);

        $cookieJar = new CookieJar($cookies);

        $cookieJar->clear($key);

        unset($cookies[$key]);

        $this->assertSame($cookies, $cookieJar->all());
    }

    public function testCanClearCookieWhereValueIsNull()
    {
        $key = $this->getFaker()->word;

        $cookies = [
            $key => null,
        ];

        $cookieJar = new CookieJar($cookies);

        $cookieJar->clear($key);

        $this->assertSame([], $cookieJar->all());
    }

    public function testCanClearCookieEvenIfKeyDoesNotExist()
    {
        $key = $this->getFaker()->word;

        $cookieJar = new CookieJar();

        $cookieJar->clear($key);

        $this->assertSame([], $cookieJar->all());
    }

    public function testCanClearAllCookies()
    {
        $cookies = $this->cookies();

        $cookieJar = new CookieJar($cookies);

        $cookieJar->clearAll();

        $this->assertSame([], $cookieJar->all());
    }

    /**
     * @param int $count
     *
     * @return array
     */
    private function cookies($count = 5)
    {
        $faker = $this->getFaker();

        $count = $count ?: $faker->numberBetween(5, 15);

        return \array_combine(
            $faker->words($count),
            $faker->words($count)
        );
    }
}
