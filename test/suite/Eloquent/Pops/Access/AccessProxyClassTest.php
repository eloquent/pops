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
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Access\AccessProxyClass
 * @covers Eloquent\Pops\ProxyClass
 */
class AccessProxyClassTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->_class = 'Eloquent\Pops\Test\Fixture\Object';
        $this->_proxy = new AccessProxyClass($this->_class);
    }

    public function fixtureData()
    {
        $data = array();

        // #0: class with no inheritance
        $class = 'Eloquent\Pops\Test\Fixture\Object';
        $proxy = new AccessProxyClass($class);
        $data[] = array($class, $proxy);

        // #1: child class
        $class = 'Eloquent\Pops\Test\Fixture\ChildObject';
        $proxy = new AccessProxyClass($class);
        $data[] = array($class, $proxy);

        return $data;
    }

    /**
     * @dataProvider fixtureData
     */
    public function testRecursive($class)
    {
        $recursiveProxy = new AccessProxyClass($class, true);

        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $recursiveProxy->staticObject()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $recursiveProxy->staticObject()->object()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $recursiveProxy->staticObject()->arrayValue()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $recursiveProxy->staticObject()->string()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $recursiveProxy->staticArray()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $recursiveProxy->staticString()
        );
    }

    /**
     * @dataProvider fixtureData
     */
    public function testCall($class, AccessProxyClass $proxy)
    {
        $this->assertPopsProxyCall(
            $proxy,
            'staticPublicMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $proxy,
            'staticProtectedMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $proxy,
            'staticPrivateMethod',
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
    public function testSetGet($class, AccessProxyClass $proxy)
    {
        $this->assertTrue(isset($proxy->staticPublicProperty));
        $this->assertTrue(isset($proxy->staticProtectedProperty));
        $this->assertTrue(isset($proxy->staticPrivateProperty));
        $this->assertEquals(
            'staticPublicProperty',
            $proxy->staticPublicProperty
        );
        $this->assertEquals(
            'staticProtectedProperty',
            $proxy->staticProtectedProperty
        );
        $this->assertEquals(
            'staticPrivateProperty',
            $proxy->staticPrivateProperty
        );

        $proxy->staticPublicProperty = 'foo';
        $proxy->staticProtectedProperty = 'bar';
        $proxy->staticPrivateProperty = 'baz';

        $this->assertTrue(isset($proxy->staticPublicProperty));
        $this->assertTrue(isset($proxy->staticProtectedProperty));
        $this->assertTrue(isset($proxy->staticPrivateProperty));
        $this->assertEquals('foo', $proxy->staticPublicProperty);
        $this->assertEquals('bar', $proxy->staticProtectedProperty);
        $this->assertEquals('baz', $proxy->staticPrivateProperty);

        unset($proxy->staticPublicProperty);
        unset($proxy->staticProtectedProperty);
        unset($proxy->staticPrivateProperty);

        $this->assertFalse(isset($proxy->staticPublicProperty));
        $this->assertFalse(isset($proxy->staticProtectedProperty));
        $this->assertFalse(isset($proxy->staticPrivateProperty));

        $proxy->staticPublicProperty = 'staticPublicProperty';
        $proxy->staticProtectedProperty = 'staticProtectedProperty';
        $proxy->staticPrivateProperty = 'staticPrivateProperty';

        $this->assertTrue(isset($proxy->staticPublicProperty));
        $this->assertTrue(isset($proxy->staticProtectedProperty));
        $this->assertTrue(isset($proxy->staticPrivateProperty));
        $this->assertEquals(
            'staticPublicProperty',
            $proxy->staticPublicProperty
        );
        $this->assertEquals(
            'staticProtectedProperty',
            $proxy->staticProtectedProperty
        );
        $this->assertEquals(
            'staticPrivateProperty',
            $proxy->staticPrivateProperty
        );

        $this->assertFalse(isset($proxy->foo));
    }

    public function setGetFailureData()
    {
        return array(
            array('__set', array('foo', 'bar')),
            array('__get', array('foo')),
            array('__unset', array('foo')),
        );
    }

    /**
     * @dataProvider setGetFailureData
     */
    public function testSetGetFailure($method, array $arguments)
    {
        $this->setExpectedException(
            'LogicException',
            'Access to undeclared static property: '.
                'Eloquent\Pops\Test\Fixture\Object::$'.
                $arguments[0]
        );
        call_user_func_array(array($this->_proxy, $method), $arguments);
    }

    public function testPopsGenerateStaticClassProxy()
    {
        $class = AccessProxyClass::popsGenerateStaticClassProxy(
            'Eloquent\Pops\Test\Fixture\Object'
        );

        $this->assertTrue(class_exists($class, false));
        $this->assertTrue(
            is_subclass_of($class, 'Eloquent\Pops\Access\AccessProxyClass')
        );

        $expected = new $class('Eloquent\Pops\Test\Fixture\Object');
        $proxy = $class::popsProxy();

        $this->assertEquals($expected, $proxy);

        // recursive tests
        $class = AccessProxyClass::popsGenerateStaticClassProxy(
            'Eloquent\Pops\Test\Fixture\Object',
            true
        );

        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $class::staticObject()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyObject',
            $class::staticObject()->object()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $class::staticObject()->arrayValue()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $class::staticObject()->string()
        );
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyArray',
            $class::staticArray()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $class::staticString()
        );
    }
}
