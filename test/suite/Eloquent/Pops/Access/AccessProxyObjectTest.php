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

use Eloquent\Pops\Test\Fixture\ChildObject;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\Fixture\Overload;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Access\AccessProxyObject
 * @covers Eloquent\Pops\ProxyObject
 */
class AccessProxyObjectTest extends TestCase
{
    public function fixtureData()
    {
        $data = array();

        // #0: object with no inheritance
        $object = new Object;
        $proxy = new AccessProxyObject($object);
        $data[] = array($object, $proxy);

        // #1: child object
        $object = new ChildObject;
        $proxy = new AccessProxyObject($object);
        $data[] = array($object, $proxy);

        return $data;
    }

    /**
     * @dataProvider fixtureData
     */
    public function testRecursive(Object $object)
    {
        $recursiveProxy = new AccessProxyObject($object, true);

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

    /**
     * @dataProvider fixtureData
     */
    public function testCall(Object $object, AccessProxyObject $proxy)
    {
        $this->assertPopsProxyCall(
            $proxy,
            'publicMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $proxy,
            'protectedMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $proxy,
            'privateMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $proxy,
            'foo',
            array('bar', 'baz'),
            true
        );
    }

    /**
     * @dataProvider fixtureData
     */
    public function testSetGet(Object $object, AccessProxyObject $proxy)
    {
        $this->assertTrue(isset($proxy->publicProperty));
        $this->assertTrue(isset($proxy->protectedProperty));
        $this->assertTrue(isset($proxy->privateProperty));
        $this->assertEquals(
            'publicProperty',
            $proxy->publicProperty
        );
        $this->assertEquals(
            'protectedProperty',
            $proxy->protectedProperty
        );
        $this->assertEquals(
            'privateProperty',
            $proxy->privateProperty
        );

        $proxy->publicProperty = 'foo';
        $proxy->protectedProperty = 'bar';
        $proxy->privateProperty = 'baz';

        $this->assertTrue(isset($proxy->publicProperty));
        $this->assertTrue(isset($proxy->protectedProperty));
        $this->assertTrue(isset($proxy->privateProperty));
        $this->assertEquals('foo', $proxy->publicProperty);
        $this->assertEquals('bar', $proxy->protectedProperty);
        $this->assertEquals('baz', $proxy->privateProperty);

        unset($proxy->publicProperty);
        unset($proxy->protectedProperty);
        unset($proxy->privateProperty);

        $this->assertFalse(isset($proxy->publicProperty));
        $this->assertFalse(isset($proxy->protectedProperty));
        $this->assertFalse(isset($proxy->privateProperty));

        $proxy->foo = 'bar';

        $this->assertTrue(isset($proxy->foo));
        $this->assertTrue(isset($object->foo));
        $this->assertEquals('bar', $proxy->foo);
        $this->assertEquals('bar', $object->foo);

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
