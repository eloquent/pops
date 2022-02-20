<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Safe;

use Eloquent\Pops\Test\Fixture\Obj;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Safe\SafeProxy
 * @covers Eloquent\Pops\Safe\SafeProxyArray
 * @covers Eloquent\Pops\Safe\SafeProxyClass
 * @covers Eloquent\Pops\Safe\SafeProxyObject
 * @covers Eloquent\Pops\Safe\SafeProxyPrimitive
 */
class SafeProxyTest extends TestCase
{
    public function testProxy()
    {
        $expected = new SafeProxyClass('Eloquent\Pops\Test\Fixture\Obj');

        $this->assertEquals($expected, SafeProxy::proxyClass('Eloquent\Pops\Test\Fixture\Obj'));

        $class = SafeProxy::proxyClassStatic('Eloquent\Pops\Test\Fixture\Obj');

        $this->assertTrue(class_exists($class));
        $this->assertTrue(is_subclass_of($class, 'Eloquent\Pops\Safe\SafeProxyClass'));

        $expected = new SafeProxyArray([]);

        $this->assertEquals($expected, SafeProxy::proxy([]));
        $this->assertEquals($expected, SafeProxy::proxyArray([]));

        $expected = new SafeProxyObject(new Obj());

        $this->assertEquals($expected, SafeProxy::proxy(new Obj()));
        $this->assertEquals($expected, SafeProxy::proxyObject(new Obj()));

        $expected = new SafeProxyPrimitive('string');

        $this->assertEquals($expected, SafeProxy::proxy('string'));
        $this->assertEquals($expected, SafeProxy::proxyPrimitive('string'));
    }

    public function testArrayRecursion()
    {
        $proxy = SafeProxy::proxy(['foo' => 'bar'], true);
        $expected = SafeProxy::proxy('bar', true);

        $this->assertEquals($expected, $proxy['foo']);
    }

    public function testObjectRecursion()
    {
        $object = new Obj();
        $proxy = SafeProxy::proxy($object, true);
        $expected = SafeProxy::proxy($object->string(), true);

        $this->assertEquals($expected, $proxy->string());
    }

    public function testClassRecursion()
    {
        $class = 'Eloquent\Pops\Test\Fixture\Obj';
        $proxy = SafeProxy::proxyClassStatic($class, true);
        $expected = SafeProxy::proxy(Obj::staticString(), true);

        $this->assertEquals($expected, $proxy::staticString());
    }
}
