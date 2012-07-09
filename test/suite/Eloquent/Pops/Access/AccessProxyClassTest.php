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

    public function testRecursive()
    {
        $recursiveProxy = new AccessProxyClass($this->_class, true);

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

    public function testCall()
    {
        $this->assertInstanceOf(
            __NAMESPACE__.'\AccessProxyClass',
            $this->_proxy
        );

        $this->assertPopsProxyCall(
            $this->_proxy,
            'staticPublicMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $this->_proxy,
            'staticProtectedMethod',
            array('foo', 'bar')
        );
        $this->assertPopsProxyCall(
            $this->_proxy,
            'staticPrivateMethod',
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
        $this->assertTrue(isset($this->_proxy->staticPublicProperty));
        $this->assertTrue(isset($this->_proxy->staticProtectedProperty));
        $this->assertTrue(isset($this->_proxy->staticPrivateProperty));
        $this->assertEquals(
            'staticPublicProperty',
            $this->_proxy->staticPublicProperty
        );
        $this->assertEquals(
            'staticProtectedProperty',
            $this->_proxy->staticProtectedProperty
        );
        $this->assertEquals(
            'staticPrivateProperty',
            $this->_proxy->staticPrivateProperty
        );

        $this->_proxy->staticPublicProperty = 'foo';
        $this->_proxy->staticProtectedProperty = 'bar';
        $this->_proxy->staticPrivateProperty = 'baz';

        $this->assertTrue(isset($this->_proxy->staticPublicProperty));
        $this->assertTrue(isset($this->_proxy->staticProtectedProperty));
        $this->assertTrue(isset($this->_proxy->staticPrivateProperty));
        $this->assertEquals('foo', $this->_proxy->staticPublicProperty);
        $this->assertEquals('bar', $this->_proxy->staticProtectedProperty);
        $this->assertEquals('baz', $this->_proxy->staticPrivateProperty);

        unset($this->_proxy->staticPublicProperty);
        unset($this->_proxy->staticProtectedProperty);
        unset($this->_proxy->staticPrivateProperty);

        $this->assertFalse(isset($this->_proxy->staticPublicProperty));
        $this->assertFalse(isset($this->_proxy->staticProtectedProperty));
        $this->assertFalse(isset($this->_proxy->staticPrivateProperty));

        $this->_proxy->staticPublicProperty = 'staticPublicProperty';
        $this->_proxy->staticProtectedProperty = 'staticProtectedProperty';
        $this->_proxy->staticPrivateProperty = 'staticPrivateProperty';

        $this->assertTrue(isset($this->_proxy->staticPublicProperty));
        $this->assertTrue(isset($this->_proxy->staticProtectedProperty));
        $this->assertTrue(isset($this->_proxy->staticPrivateProperty));
        $this->assertEquals(
            'staticPublicProperty',
            $this->_proxy->staticPublicProperty
        );
        $this->assertEquals(
            'staticProtectedProperty',
            $this->_proxy->staticProtectedProperty
        );
        $this->assertEquals(
            'staticPrivateProperty',
            $this->_proxy->staticPrivateProperty
        );

        $this->assertFalse(isset($this->_proxy->foo));
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
