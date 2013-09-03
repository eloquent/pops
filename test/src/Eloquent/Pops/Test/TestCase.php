<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Test;

use PHPUnit_Framework_TestCase;
use Eloquent\Pops\ProxyInterface;
use Eloquent\Pops\ProxyClass;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @param ProxyInterface $proxy
     * @param string         $method
     * @param array          $arguments
     * @param boolean        $magic
     */
    protected function assertPopsProxyCall(
        ProxyInterface $proxy,
        $method,
        array $arguments = null,
        $magic = null
    ) {
        $actual = call_user_func_array(array($proxy, $method), $arguments);

        if ($magic) {
            $arguments = array($method, $arguments);

            if ($proxy instanceof ProxyClass) {
                $method = '__callStatic';
            } else {
                $method = '__call';
            }
        }

        $expected = array($method, $arguments);

        $this->assertEquals($expected, $actual);
    }
}
