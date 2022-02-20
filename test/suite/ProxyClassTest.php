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

use Eloquent\Pops\Test\Fixture\Obj;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers \Eloquent\Pops\ProxyClass
 * @covers \Eloquent\Pops\AbstractProxy
 */
class ProxyClassTest extends TestCase
{
    protected function setUp(): void
    {
        $this->class = 'Eloquent\Pops\Test\Fixture\Obj';
        $this->proxy = new ProxyClass($this->class);
        $this->recursiveProxy = new ProxyClass($this->class, true);
    }

    public function testCallStaticFailure()
    {
        $this->expectException('LogicException');
        ProxyClass::foo();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf('Eloquent\Pops\ProxyClass', $this->proxy);
        $this->assertEquals($this->class, $this->proxy->popsClass());
    }

    public function testConstructFailureType()
    {
        $this->expectException('Eloquent\Pops\Exception\InvalidTypeException');
        new ProxyClass('foo');
    }

    public function testCall()
    {
        $this->assertPopsProxyCall($this->proxy, 'staticPublicMethod', ['foo', 'bar']);
        $this->assertPopsProxyCall($this->proxy, 'foo', ['bar', 'baz'], true);

        // recursive tests
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $this->recursiveProxy->staticObject());
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $this->recursiveProxy->staticObject()->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $this->recursiveProxy->staticObject()->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $this->recursiveProxy->staticObject()->string());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $this->recursiveProxy->staticArray());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $this->recursiveProxy->staticString());
    }

    public function testPopsCallByReference()
    {
        $variable = null;
        $arguments = [&$variable, 'foo'];
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
        $staticPublicProperty = Obj::$staticPublicProperty;
        Obj::$staticPublicProperty = new Obj();

        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $this->recursiveProxy->staticPublicProperty);
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $this->recursiveProxy->staticPublicProperty->object());
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyArray',
            $this->recursiveProxy->staticPublicProperty->arrayValue()
        );
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $this->recursiveProxy->staticPublicProperty->string()
        );

        Obj::$staticPublicProperty = [
            'object' => new Obj(),
            'array' => [],
            'string' => 'string',
        ];

        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $this->recursiveProxy->staticPublicProperty);
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $this->recursiveProxy->staticPublicProperty['object']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $this->recursiveProxy->staticPublicProperty['array']);
        $this->assertInstanceOf(
            'Eloquent\Pops\ProxyPrimitive',
            $this->recursiveProxy->staticPublicProperty['string']
        );

        Obj::$staticPublicProperty = 'string';

        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $this->recursiveProxy->staticPublicProperty);

        Obj::$staticPublicProperty = $staticPublicProperty;
    }

    public function testPopsGenerateStaticClassProxy()
    {
        $class = ProxyClass::popsGenerateStaticClassProxy('Eloquent\Pops\Test\Fixture\Obj');

        $this->assertTrue(class_exists($class, false));
        $this->assertTrue(is_subclass_of($class, 'Eloquent\Pops\ProxyClass'));

        $expected = new $class('Eloquent\Pops\Test\Fixture\Obj');
        $proxy = $class::popsProxy();

        $this->assertEquals($expected, $proxy);
        $this->assertSame($proxy, $class::popsProxy());

        $this->assertEquals(
            ['staticPublicMethod', ['foo', 'bar']],
            $class::staticPublicMethod('foo', 'bar')
        );
        $this->assertEquals(
            ['__callStatic', ['foo', ['bar', 'baz']]],
            $class::foo('bar', 'baz')
        );

        // recursive tests
        $class = ProxyClass::popsGenerateStaticClassProxy('Eloquent\Pops\Test\Fixture\Obj', true);

        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $class::staticObject());
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $class::staticObject()->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $class::staticObject()->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $class::staticObject()->string());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $class::staticArray());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $class::staticString());

        $class = uniqid('Foo');
        ProxyClass::popsGenerateStaticClassProxy('Eloquent\Pops\Test\Fixture\Obj', true, $class);

        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $class::staticObject());
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $class::staticObject()->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $class::staticObject()->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $class::staticObject()->string());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $class::staticArray());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $class::staticString());

        // custom class name
        $class = uniqid('Foo');
        ProxyClass::popsGenerateStaticClassProxy('Eloquent\Pops\Test\Fixture\Obj', null, $class);

        $this->assertTrue(class_exists($class, false));
        $this->assertTrue(is_subclass_of($class, 'Eloquent\Pops\ProxyClass'));
    }

    public function testToString()
    {
        $this->assertSame($this->class, strval($this->proxy));
    }
}
