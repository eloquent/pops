<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops;

use Eloquent\Pops\Safe\SafeProxy;
use Eloquent\Pops\Test\Fixture\Obj;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    public function testProxy()
    {
        $safe = SafeProxy::proxy(new Obj(), true);

        $this->assertSame($safe, Proxy::proxy($safe));
        $this->assertEquals($safe->object(), Proxy::proxy($safe->object()));
        $this->assertEquals($safe->object(), Proxy::proxy($safe)->object());

        $expected = new ProxyObject(new Obj());

        $this->assertEquals($expected, Proxy::proxy(new Obj()));
        $this->assertEquals($expected, Proxy::proxyObject(new Obj()));

        $expected = new ProxyArray([]);

        $this->assertEquals($expected, Proxy::proxy([]));
        $this->assertEquals($expected, Proxy::proxyArray([]));

        $expected = new ProxyPrimitive('string');

        $this->assertEquals($expected, Proxy::proxy('string'));
        $this->assertEquals($expected, Proxy::proxyPrimitive('string'));
    }

    public function testProxyClass()
    {
        $expected = new ProxyClass('Eloquent\Pops\Test\Fixture\Obj');

        $this->assertEquals($expected, Proxy::proxyClass('Eloquent\Pops\Test\Fixture\Obj'));
    }

    public function testProxyClassStatic()
    {
        $class = Proxy::proxyClassStatic('Eloquent\Pops\Test\Fixture\Obj');

        $this->assertTrue(class_exists($class));
        $this->assertTrue(is_subclass_of($class, 'Eloquent\Pops\ProxyClass'));
    }
}
