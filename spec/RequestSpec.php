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

/**
 * @mixin Request
 */
class RequestSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Request::class);
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
