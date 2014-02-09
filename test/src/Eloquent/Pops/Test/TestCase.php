<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Test;

use Eloquent\Pops\ProxyClassInterface;
use Eloquent\Pops\ProxyInterface;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Assert that a Pops call was made as expected.
     *
     * @param ProxyInterface $proxy     The proxy to call.
     * @param string         $method    The method to call.
     * @param array|null     $arguments The arguments to pass.
     * @param boolean|null   $isMagic   True if the call should be handled via a magic method.
     */
    protected function assertPopsProxyCall(
        ProxyInterface $proxy,
        $method,
        array $arguments = null,
        $isMagic = null
    ) {
        $actual = call_user_func_array(array($proxy, $method), $arguments);

        if ($isMagic) {
            $arguments = array($method, $arguments);

            if ($proxy instanceof ProxyClassInterface) {
                $method = '__callStatic';
            } else {
                $method = '__call';
            }
        }

        $expected = array($method, $arguments);

        $this->assertSame($expected, $actual);
    }
}
