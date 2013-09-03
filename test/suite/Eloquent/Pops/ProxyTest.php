<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use Eloquent\Pops\Safe\SafeProxy;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;

class ProxyTest extends TestCase
{
    public function testProxy()
    {
        $safe = SafeProxy::proxy(new Object, true);

        $this->assertSame($safe, Proxy::proxy($safe));
        $this->assertEquals($safe->object(), Proxy::proxy($safe->object()));
        $this->assertEquals($safe->object(), Proxy::proxy($safe)->object());

        $expected = new ProxyObject(new Object);

        $this->assertEquals($expected, Proxy::proxy(new Object));
        $this->assertEquals($expected, Proxy::proxyObject(new Object));

        $expected = new ProxyArray(array());

        $this->assertEquals($expected, Proxy::proxy(array()));
        $this->assertEquals($expected, Proxy::proxyArray(array()));

        $expected = new ProxyPrimitive('string');

        $this->assertEquals($expected, Proxy::proxy('string'));
        $this->assertEquals($expected, Proxy::proxyPrimitive('string'));
    }

    public function testProxyClass()
    {
        $expected = new ProxyClass(__NAMESPACE__ . '\Test\Fixture\Object');

        $this->assertEquals($expected, Proxy::proxyClass(__NAMESPACE__ . '\Test\Fixture\Object'));
    }

    public function testProxyClassStatic()
    {
        $class = Proxy::proxyClassStatic(__NAMESPACE__ . '\Test\Fixture\Object');

        $this->assertTrue(class_exists($class));
        $this->assertTrue(is_subclass_of($class, __NAMESPACE__ . '\ProxyClass'));
    }
}
