<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Refinery29\Piston\Test\Unit;

use Refinery29\Piston\CookieJar;
use Refinery29\Piston\Middleware\Request\Sorts;
use Refinery29\Piston\Request;
use Refinery29\Test\Util\Faker\GeneratorTrait;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    use GeneratorTrait;

    public function testDefaults()
    {
        $request = new Request();

        $this->assertSame([], $request->getOffsetLimit());
    }

    public function testCanSetRequestedFields()
    {
        $requestedFields = $this->getFaker()->words;

        $request = new Request();

        $request->setRequestedFields($requestedFields);

        $this->assertSame($requestedFields, $request->getRequestedFields());
    }

    public function testRequestedFieldsIsArrayByDefault()
    {
        $request = new Request();

        $this->assertSame([], $request->getRequestedFields());
    }

    public function testIncludedResourcesIsArrayByDefault()
    {
        $request = new Request();

        $this->assertSame([], $request->getIncludedResources());
    }

    public function testWithRequestedFieldsClonesRequestAndSetsRequestedFields()
    {
        $requestedFields = $this->getFaker()->words;

        $request = new Request();

        $mutated = $request->withRequestedFields($requestedFields);

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);
        $this->assertSame($requestedFields, $mutated->getRequestedFields());
    }

    public function testCanSetIncludedResources()
    {
        $includedResources = $this->getFaker()->words;

        $request = new Request();

        $request->setIncludedResources($includedResources);

        $this->assertSame($includedResources, $request->getIncludedResources());
    }

    public function testWithIncludedResourcesClonesRequestAndSetsIncludedResources()
    {
        $includedResources = $this->getFaker()->words;

        $request = new Request();

        $mutated = $request->withIncludedResources($includedResources);

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);
        $this->assertSame($includedResources, $mutated->getIncludedResources());
    }

    public function testCanSetBeforeCursor()
    {
        $beforeCursor = $this->getFaker()->numberBetween(0);

        $request = new Request();

        $request->setBeforeCursor($beforeCursor);

        $expected = [
            'before' => $beforeCursor,
        ];

        $this->assertSame($expected, $request->getPaginationCursor());
    }

    public function testWithBeforeCursorClonesRequestAndSetsBeforeCursor()
    {
        $beforeCursor = $this->getFaker()->word;

        $request = new Request();

        $mutated = $request->withBeforeCursor($beforeCursor);

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);
        $this->assertSame($beforeCursor, $mutated->getBeforeCursor());
        $this->assertSame(Request::CURSOR_PAGINATION, $mutated->getPaginationType());
    }

    public function testCanSetAfterCursor()
    {
        $afterCursor = $this->getFaker()->numberBetween(0);

        $request = new Request();

        $request->setAfterCursor($afterCursor);

        $expected = [
            'after' => $afterCursor,
        ];

        $this->assertSame($expected, $request->getPaginationCursor());
    }

    public function testWithAfterCursorClonesRequestAndSetsAfterCursor()
    {
        $afterCursor = $this->getFaker()->word;

        $request = new Request();

        $mutated = $request->withAfterCursor($afterCursor);

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);
        $this->assertSame($afterCursor, $mutated->getAfterCursor());
        $this->assertSame(Request::CURSOR_PAGINATION, $mutated->getPaginationType());
    }

    /**
     * @dataProvider offsetOrLimitProvider
     *
     * @param int $offset
     * @param int $limit
     */
    public function testCanSetEitherOffsetOrLimitAndReturnAResult($offset, $limit)
    {
        $request = (new Request())->withOffsetLimit($offset, $limit);

        $this->assertSame(
            [
                'offset' => $offset,
                'limit' => $limit,
            ],
            $request->getOffsetLimit()
        );
    }

    public function offsetOrLimitProvider()
    {
        $faker = $this->getFaker();

        return [
            [
                'offset' => $faker->numberBetween(0, 9000),
                'limit' => null,
            ],
            [
                'offset' => null,
                'limit' => $faker->numberBetween(1, 9000),
            ],
        ];
    }

    public function testCanSetOffsetLimit()
    {
        $faker = $this->getFaker();

        $offset = $faker->numberBetween(0, 9000);
        $limit = $faker->numberBetween(0, 9000);

        $request = new Request();

        $request->setOffsetLimit(
            $offset,
            $limit
        );

        $expected = [
            'offset' => $offset,
            'limit' => $limit,
        ];

        $this->assertSame($expected, $request->getOffsetLimit());
        $this->assertSame(Request::OFFSET_LIMIT_PAGINATION, $request->getPaginationType());
    }

    public function testWithOffsetLimitClonesRequestAndSetsOffsetLimit()
    {
        $faker = $this->getFaker();

        $offset = $faker->numberBetween(0, 9000);
        $limit = $faker->numberBetween(0, 9000);

        $request = new Request();

        $mutated = $request->withOffsetLimit(
            $offset,
            $limit
        );

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);

        $expected = [
            'offset' => $offset,
            'limit' => $limit,
        ];

        $this->assertSame($expected, $mutated->getOffsetLimit());
        $this->assertSame(Request::OFFSET_LIMIT_PAGINATION, $mutated->getPaginationType());
    }

    public function testGetCookieReturnsCookiesFromCookieJar()
    {
        $faker = $this->getFaker();

        $key = $faker->word;
        $value = $faker->word;

        $cookies = [
            $key => $value,
        ];

        $cookieJar = $this->getCookieJarMock();

        $cookieJar
            ->expects($this->once())
            ->method('get')
            ->willReturnCallback(function ($key) use ($cookies) {
                if (\array_key_exists($key, $cookies)) {
                    return $cookies[$key];
                }
            });

        $request = new Request($cookieJar);

        $this->assertSame($value, $request->getCookie($key));
    }

    public function testWithCookieClonesRequestAndSetsValueInCookieJar()
    {
        $faker = $this->getFaker();

        $key = $faker->word;
        $value = $faker->word;

        $request = new Request();

        /* @var Request $mutated */
        $mutated = $request->withCookie(
            $key,
            $value
        );

        $this->assertInstanceOf(Request::class, $mutated);
        $this->assertNotSame($request, $mutated);
        $this->assertSame($value, $mutated->getCookie($key));
        $this->assertSame($value, $request->getCookie($key));
    }

    public function testGetCookiesReturnsAllCookies()
    {
        $faker = $this->getFaker();

        $cookies = [];

        for ($i = 0; $i < $faker->numberBetween(1, 5); ++$i) {
            $key = $faker->unique()->word;
            $value = $faker->word;

            $cookies[$key] = $value;
        }

        $cookieJar = $this->getCookieJarMock();

        $cookieJar
            ->expects($this->once())
            ->method('all')
            ->willReturn($cookies);

        $request = new Request($cookieJar);

        $this->assertSame($cookies, $request->getCookies());
    }

    public function testGetSetSorts()
    {
        $faker = $this->getFaker();
        $sorts = [
            $faker->word => Sorts::SORT_DESCENDING,
            $faker->word => Sorts::SORT_ASCENDING,
            $faker->word => Sorts::SORT_DESCENDING,
        ];

        $request = new Request();

        $request->setSorts($sorts);

        $this->assertSame($sorts, $request->getSorts());

        foreach (\array_keys($sorts) as $sort) {
            $this->assertTrue($request->hasSort($sort));
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|CookieJar
     */
    private function getCookieJarMock()
    {
        return $this->getMockBuilder(CookieJar::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
