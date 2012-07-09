<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Access;

use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\Fixture\Overload;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Access\AccessProxyObject
 * @covers Eloquent\Pops\ProxyObject
 */
class AccessProxyObjectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_object = new Object;
        $this->_proxy = new AccessProxyObject($this->_object);
    }

    public function testRecursive()
    {
        $recursiveProxy = new AccessProxyObject($this->_object, true);

        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $recursiveProxy->object()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $recursiveProxy->object()->object()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $recursiveProxy->object()->arrayValue()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $recursiveProxy->object()->string()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $recursiveProxy->arrayValue()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $recursiveProxy->string()
        );
    }

    public function testCall()
    {
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $this->_proxy
        );

        $this->assertPopsProxyCall(
            $this->_proxy,
            'publicMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $this->_proxy,
            'protectedMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $this->_proxy,
            'privateMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $this->_proxy,
            'foo',
            array('bar', 'baz'),
            true
        );
    }

    public function testSetGet()
    {
        $this->assertTrue(isset($this->_proxy->publicProperty));
        $this->assertTrue(isset($this->_proxy->protectedProperty));
        $this->assertTrue(isset($this->_proxy->privateProperty));
        $this->assertEquals(
            'publicProperty',
            $this->_proxy->publicProperty
        );
        $this->assertEquals(
            'protectedProperty',
            $this->_proxy->protectedProperty
        );
        $this->assertEquals(
            'privateProperty',
            $this->_proxy->privateProperty
        );

        $this->_proxy->publicProperty = 'foo';
        $this->_proxy->protectedProperty = 'bar';
        $this->_proxy->privateProperty = 'baz';

        $this->assertTrue(isset($this->_proxy->publicProperty));
        $this->assertTrue(isset($this->_proxy->protectedProperty));
        $this->assertTrue(isset($this->_proxy->privateProperty));
        $this->assertEquals('foo', $this->_proxy->publicProperty);
        $this->assertEquals('bar', $this->_proxy->protectedProperty);
        $this->assertEquals('baz', $this->_proxy->privateProperty);

        unset($this->_proxy->publicProperty);
        unset($this->_proxy->protectedProperty);
        unset($this->_proxy->privateProperty);

        $this->assertFalse(isset($this->_proxy->publicProperty));
        $this->assertFalse(isset($this->_proxy->protectedProperty));
        $this->assertFalse(isset($this->_proxy->privateProperty));

        $this->_proxy->foo = 'bar';

        $this->assertTrue(isset($this->_proxy->foo));
        $this->assertTrue(isset($this->_object->foo));
        $this->assertEquals('bar', $this->_proxy->foo);
        $this->assertEquals('bar', $this->_object->foo);

        $object = new Overload;
        $object->values = array(
            'foo' => 'bar',
        );
        $proxy = new AccessProxyObject($object);

        $this->assertTrue(isset($proxy->foo));
        $this->assertEquals('bar', $proxy->foo);

        unset($proxy->foo);

        $this->assertFalse(isset($proxy->foo));

        $proxy->foo = 'baz';

        $this->assertTrue(isset($proxy->foo));
        $this->assertEquals('baz', $proxy->foo);
    }
}
