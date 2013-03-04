<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Safe;

use Eloquent\Pops\Test\Fixture\Object;
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
        $expected = new SafeProxyClass(
            'Eloquent\Pops\Test\Fixture\Object'
        );

        $this->assertEquals($expected, SafeProxy::proxyClass(
            'Eloquent\Pops\Test\Fixture\Object'
        ));

        $class = SafeProxy::proxyClassStatic(
            'Eloquent\Pops\Test\Fixture\Object'
        );

        $this->assertTrue(class_exists($class));
        $this->assertTrue(
            is_subclass_of($class, __NAMESPACE__.'\SafeProxyClass')
        );

        $expected = new SafeProxyArray(array());

        $this->assertEquals($expected, SafeProxy::proxy(array()));
        $this->assertEquals($expected, SafeProxy::proxyArray(array()));

        $expected = new SafeProxyObject(new Object);

        $this->assertEquals($expected, SafeProxy::proxy(new Object));
        $this->assertEquals($expected, SafeProxy::proxyObject(new Object));

        $expected = new SafeProxyPrimitive('string');

        $this->assertEquals($expected, SafeProxy::proxy('string'));
        $this->assertEquals($expected, SafeProxy::proxyPrimitive('string'));
    }

    public function testArrayRecursion()
    {
        $proxy = SafeProxy::proxy(array('foo' => 'bar'), true);
        $expected = SafeProxy::proxy('bar', true);

        $this->assertEquals($expected, $proxy['foo']);
    }

    public function testObjectRecursion()
    {
        $object = new Object;
        $proxy = SafeProxy::proxy($object, true);
        $expected = SafeProxy::proxy($object->string(), true);

        $this->assertEquals($expected, $proxy->string());
    }

    public function testClassRecursion()
    {
        $class = 'Eloquent\Pops\Test\Fixture\Object';
        $proxy = SafeProxy::proxyClassStatic($class, true);
        $expected = SafeProxy::proxy(Object::staticString(), true);

        $this->assertEquals($expected, $proxy::staticString());
    }
}
