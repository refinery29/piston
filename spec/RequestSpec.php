<?php

/*
 * Copyright (c) 2016 Refinery29, Inc.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */
namespace spec\Refinery29\Piston;

use PhpSpec\ObjectBehavior;
use Refinery29\Piston\CookieJar;
use Refinery29\Piston\Request;

class RequestSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Request::class);
    }

    public function it_can_set_requested_fields()
    {
        $req_fields = ['yolo', 'gogo'];
        $this->setRequestedFields($req_fields);

        $this->getRequestedFields()->shouldReturn($req_fields);
    }

    public function it_can_set_included_resources()
    {
        $included_resources = ['monica', 'chandler'];
        $this->setIncludedResources($included_resources);

        $this->getIncludedResources()->shouldReturn($included_resources);
    }

    public function it_can_set_a_before_cursor()
    {
        $pagination_cursor = rand();
        $this->setBeforeCursor($pagination_cursor);

        $this->getPaginationCursor()->shouldReturn(['before' => $pagination_cursor]);
    }

    public function it_can_set_an_after_cursor()
    {
        $pagination_cursor = rand();
        $this->setAfterCursor($pagination_cursor);

        $this->getPaginationCursor()->shouldReturn(['after' => $pagination_cursor]);
        $this->getPaginationType()->shouldReturn(Request::CURSOR_PAGINATION);
    }

    public function it_can_set_offset_limit()
    {
        $this->setOffsetLimit(10, 10);
        $this->getOffsetLimit()->shouldReturn(['offset' => 10, 'limit' => 10]);
        $this->getPaginationType()->shouldReturn(Request::OFFSET_LIMIT_PAGINATION);
    }

    public function it_can_set_an_offset_and_limit_returning_a_new_request_object()
    {
        $request = $this->withOffsetLimit(10, 10);
        $request->shouldHaveType(Request::class);
        $request->getOffsetLimit()->shouldReturn(['offset' => 10, 'limit' => 10]);
        $request->getPaginationType()->shouldReturn(Request::OFFSET_LIMIT_PAGINATION);
    }

    public function it_returns_empty_array_when_no_offset_limit_is_set()
    {
        $this->getOffsetLimit()->shouldReturn([]);
    }

    public function it_has_cookies_set_by_cookiejar()
    {
        $this->beConstructedWith(new CookieJar(['yoko' => 'ono']));

        $this->getCookie('yoko')->shouldReturn('ono');
    }

    public function it_can_set_a_cookie()
    {
        $request = $this->withCookie('yoko', 'ono');
        $request->shouldHaveType(Request::class);

        $request->getCookie('yoko')->shouldReturn('ono');
    }

    public function it_can_get_a_cookie()
    {
        $this->beConstructedWith(new CookieJar(['yoko' => 'ono']));

        $this->getCookie('yoko')->shouldReturn('ono');
    }

    public function it_can_get_all_cookies()
    {
        $this->beConstructedWith(new CookieJar(['yoko' => 'ono', 'ringo' => 'starr']));

        $this->getCookies()->shouldReturn(['yoko' => 'ono', 'ringo' => 'starr']);
    }

    public function it_can_clear_a_cookie()
    {
        $this->beConstructedWith(new CookieJar(['yoko' => 'ono', 'ringo' => 'starr']));

        $this->clearCookie('yoko');

        $this->getCookies()->shouldReturn(['ringo' => 'starr']);
    }

    public function it_can_clear_all_cookies()
    {
        $this->beConstructedWith(new CookieJar(['yoko' => 'ono', 'ringo' => 'starr']));

        $this->clearCookies();

        $this->getCookies()->shouldReturn([]);
    }
}
