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

use InvalidArgumentException;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;

class ProxyClassTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->class = __NAMESPACE__ . '\Test\Fixture\Object';
        $this->proxy = new ProxyClass($this->class);
        $this->recursiveProxy = new ProxyClass($this->class, true);
    }

    public function testCallStaticFailure()
    {
        $this->setExpectedException('LogicException');
        ProxyClass::foo();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyClass', $this->proxy);
        $this->assertEquals($this->class, $this->proxy->popsClass());
    }

    public function testCall()
    {
        $this->assertPopsProxyCall($this->proxy, 'staticPublicMethod', array('foo', 'bar'));
        $this->assertPopsProxyCall($this->proxy, 'foo', array('bar', 'baz'), true);

        // recursive tests
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->staticObject());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->staticObject()->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->staticObject()->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->staticObject()->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->staticArray());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->staticString());
    }

    public function testPopsCallByReference()
    {
        $variable = null;
        $arguments = array(&$variable, 'foo');
        $this->proxy->popsCall('staticByReference', $arguments);

        $this->assertSame('foo', $variable);
    }

    public function testSetGet()
    {
        $this->assertTrue(isset($this->proxy->staticPublicProperty));
        $this->assertEquals('staticPublicProperty', $this->proxy->staticPublicProperty);

        $this->proxy->staticPublicProperty = 'foo';

        $this->assertTrue(isset($this->proxy->staticPublicProperty));
        $this->assertEquals('foo', $this->proxy->staticPublicProperty);

        unset($this->proxy->staticPublicProperty);

        $this->assertFalse(isset($this->proxy->staticPublicProperty));

        $this->proxy->staticPublicProperty = 'staticPublicProperty';

        $this->assertTrue(isset($this->proxy->staticPublicProperty));
        $this->assertEquals('staticPublicProperty', $this->proxy->staticPublicProperty);

        // recursive tests
        $staticPublicProperty = Object::$staticPublicProperty;
        Object::$staticPublicProperty = new Object;

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->staticPublicProperty);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->staticPublicProperty->object());
        $this->assertInstanceOf(
            __NAMESPACE__ . '\ProxyArray',
            $this->recursiveProxy->staticPublicProperty->arrayValue()
        );
        $this->assertInstanceOf(
            __NAMESPACE__ . '\ProxyPrimitive',
            $this->recursiveProxy->staticPublicProperty->string()
        );

        Object::$staticPublicProperty = array(
            'object' => new Object,
            'array' => array(),
            'string' => 'string',
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->staticPublicProperty);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->staticPublicProperty['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->staticPublicProperty['array']);
        $this->assertInstanceOf(
            __NAMESPACE__ . '\ProxyPrimitive',
            $this->recursiveProxy->staticPublicProperty['string']
        );

        Object::$staticPublicProperty = 'string';

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->staticPublicProperty);

        Object::$staticPublicProperty = $staticPublicProperty;
    }

    public function testPopsGenerateStaticClassProxy()
    {
        $class = ProxyClass::popsGenerateStaticClassProxy(__NAMESPACE__ . '\Test\Fixture\Object');

        $this->assertTrue(class_exists($class, false));
        $this->assertTrue(is_subclass_of($class, __NAMESPACE__ . '\ProxyClass'));

        $expected = new $class(__NAMESPACE__ . '\Test\Fixture\Object');
        $proxy = $class::popsProxy();

        $this->assertEquals($expected, $proxy);
        $this->assertSame($proxy, $class::popsProxy());

        $this->assertEquals(
            array('staticPublicMethod', array('foo', 'bar')),
            $class::staticPublicMethod('foo', 'bar')
        );
        $this->assertEquals(
            array('__callStatic', array('foo', array('bar', 'baz'))),
            $class::foo('bar', 'baz')
        );

        // recursive tests
        $class = ProxyClass::popsGenerateStaticClassProxy(__NAMESPACE__ . '\Test\Fixture\Object', true);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $class::staticObject());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $class::staticObject()->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $class::staticObject()->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $class::staticObject()->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $class::staticArray());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $class::staticString());

        $class = uniqid('Foo');
        ProxyClass::popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object', true, $class);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $class::staticObject());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $class::staticObject()->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $class::staticObject()->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $class::staticObject()->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $class::staticArray());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $class::staticString());

        // custom class name
        $class = uniqid('Foo');
        ProxyClass::popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object', null, $class);

        $this->assertTrue(class_exists($class, false));
        $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
    }
}
