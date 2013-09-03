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
use Eloquent\Pops\Test\Fixture\ArrayAccess;
use Eloquent\Pops\Test\Fixture\CallableObject;
use Eloquent\Pops\Test\Fixture\Countable;
use Eloquent\Pops\Test\Fixture\Iterator;
use Eloquent\Pops\Test\Fixture\IteratorAggregate;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\Fixture\Overload;
use Eloquent\Pops\Test\Fixture\Stringable;
use Eloquent\Pops\Test\Fixture\Uppercase\UppercaseProxy;
use Eloquent\Pops\Test\TestCase;

class ProxyObjectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->object = new Object;
        $this->proxy = new ProxyObject($this->object);
        $this->recursiveProxy = new ProxyObject($this->object, true);
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->proxy);
        $this->assertSame($this->object, $this->proxy->popsObject());
    }

    public function testConstructFailureObjectType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Provided value is not an object');
        new ProxyObject('foo');
    }

    public function testConstructFailureRecursiveType()
    {
        $this->setExpectedException('InvalidArgumentException', 'Provided value is not a boolean');
        new ProxyObject($this->object, 'foo');
    }

    public function testCall()
    {
        $this->assertPopsProxyCall($this->proxy, 'publicMethod', array('foo', 'bar'));
        $this->assertPopsProxyCall($this->proxy, 'foo', array('bar', 'baz'), true);

        // recursive tests
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->object()->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->object()->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->object()->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->string());
    }

    public function testPopsCallByReference()
    {
        $variable = null;
        $arguments = array(&$variable, 'foo');
        $this->proxy->popsCall('byReference', $arguments);

        $this->assertSame('foo', $variable);
    }

    public function testSetGet()
    {
        $this->assertEquals('publicProperty', $this->proxy->publicProperty);

        $this->assertFalse(isset($this->object->foo));
        $this->assertFalse(isset($this->proxy->foo));

        $this->object->foo = 'bar';

        $this->assertTrue(isset($this->object->foo));
        $this->assertTrue(isset($this->proxy->foo));
        $this->assertEquals('bar', $this->object->foo);
        $this->assertEquals('bar', $this->proxy->foo);

        $this->proxy->foo = 'baz';

        $this->assertTrue(isset($this->object->foo));
        $this->assertTrue(isset($this->proxy->foo));
        $this->assertEquals('baz', $this->object->foo);
        $this->assertEquals('baz', $this->proxy->foo);

        unset($this->object->foo);

        $this->assertFalse(isset($this->object->foo));
        $this->assertFalse(isset($this->proxy->foo));

        $this->proxy->foo = 'qux';

        $this->assertTrue(isset($this->object->foo));
        $this->assertTrue(isset($this->proxy->foo));
        $this->assertEquals('qux', $this->object->foo);
        $this->assertEquals('qux', $this->proxy->foo);

        unset($this->proxy->foo);

        $this->assertFalse(isset($this->object->foo));
        $this->assertFalse(isset($this->proxy->foo));

        $object = new Overload;
        $object->values = array(
            'foo' => 'bar',
            'baz' => 'qux',
        );
        $proxy = new ProxyObject($object);

        $this->assertTrue(isset($proxy->foo));
        $this->assertTrue(isset($proxy->baz));
        $this->assertEquals('bar', $proxy->foo);
        $this->assertEquals('qux', $proxy->baz);

        unset($proxy->foo);
        unset($proxy->baz);

        $this->assertFalse(isset($proxy->foo));
        $this->assertFalse(isset($proxy->baz));

        $proxy->foo = 'doom';

        $this->assertTrue(isset($proxy->foo));
        $this->assertEquals('doom', $proxy->foo);

        // recursive tests
        $this->object->publicProperty = new Object;

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->publicProperty);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->publicProperty->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->publicProperty->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->publicProperty->string());

        $this->object->publicProperty = array(
            'object' => new Object,
            'array' => array(
                'object' => new Object,
                'array' => array(),
                'string' => 'string',
            ),
            'string' => 'string',
        );

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->publicProperty);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->publicProperty['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->publicProperty['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $this->recursiveProxy->publicProperty['array']['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $this->recursiveProxy->publicProperty['array']['array']);
        $this->assertInstanceOf(
            __NAMESPACE__ . '\ProxyPrimitive',
            $this->recursiveProxy->publicProperty['array']['string']
        );
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $this->recursiveProxy->publicProperty['string']);

        $this->object->publicProperty = 'string';

        $this->assertInstanceOf(
            __NAMESPACE__.'\ProxyPrimitive',
            $this->recursiveProxy->publicProperty
        );

        $object = new Overload;
        $object->values = array(
            'object' => new Object,
            'array' => array(
                'object' => new Object,
                'array' => array(),
                'string' => 'string',
            ),
            'string' => 'string',
        );
        $recursiveProxy = new ProxyObject($object, true);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject',$recursiveProxy->object);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject',$recursiveProxy->object->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray',$recursiveProxy->object->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive',$recursiveProxy->object->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray',$recursiveProxy->array);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject',$recursiveProxy->array['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray',$recursiveProxy->array['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive',$recursiveProxy->array['string']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive',$recursiveProxy->string);
    }

    public function testOffsetSetGet()
    {
        $arrayaccess = new ArrayAccess;
        $proxy = new ProxyObject($arrayaccess);

        $this->assertFalse(isset($arrayaccess['foo']));
        $this->assertFalse(isset($proxy['foo']));

        $arrayaccess['foo'] = null;

        $this->assertTrue(isset($arrayaccess['foo']));
        $this->assertTrue(isset($proxy['foo']));
        $this->assertNull($arrayaccess['foo']);
        $this->assertNull($proxy['foo']);

        $arrayaccess['foo'] = 'bar';

        $this->assertTrue(isset($arrayaccess['foo']));
        $this->assertTrue(isset($proxy['foo']));
        $this->assertEquals('bar', $arrayaccess['foo']);
        $this->assertEquals('bar', $proxy['foo']);

        $proxy['foo'] = 'baz';

        $this->assertTrue(isset($arrayaccess['foo']));
        $this->assertTrue(isset($proxy['foo']));
        $this->assertEquals('baz', $arrayaccess['foo']);
        $this->assertEquals('baz', $proxy['foo']);

        unset($arrayaccess['foo']);

        $this->assertFalse(isset($arrayaccess['foo']));
        $this->assertFalse(isset($proxy['foo']));

        $proxy['foo'] = 'qux';

        $this->assertTrue(isset($arrayaccess['foo']));
        $this->assertTrue(isset($proxy['foo']));
        $this->assertEquals('qux', $arrayaccess['foo']);
        $this->assertEquals('qux', $proxy['foo']);

        unset($proxy['foo']);

        $this->assertFalse(isset($arrayaccess['foo']));
        $this->assertFalse(isset($proxy['foo']));

        // recursive tests
        $arrayaccess = new ArrayAccess;
        $arrayaccess['object'] = new Object;
        $arrayaccess['array'] = array(
            'object' => new Object,
            'array' => array(),
            'string' => 'string',
        );
        $arrayaccess['string'] = 'string';
        $proxy = new ProxyObject($arrayaccess, true);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $proxy['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $proxy['object']->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $proxy['object']->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $proxy['object']->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $proxy['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $proxy['array']['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $proxy['array']['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $proxy['array']['string']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $proxy['string']);
    }

    /**
     * @covers Eloquent\Pops\ProxyObject::count
     */
    public function testCount()
    {
        $countable = new Countable;
        $countable->count = 666;
        $proxy = new ProxyObject($countable);

        $this->assertEquals(666, count($proxy));
    }

    public function testIterator()
    {
        $iterator = new Iterator(
            array(
                'foo' => 'bar',
                'baz' => 'qux',
            )
        );
        $proxy = new ProxyObject($iterator);

        $this->assertEquals($iterator->values, iterator_to_array($proxy));

        $iteratorAggregate = new IteratorAggregate(
            array(
                'foo' => 'bar',
                'baz' => 'qux',
            )
        );
        $proxy = new ProxyObject($iteratorAggregate);

        $this->assertEquals($iteratorAggregate->values, iterator_to_array($proxy));

        // recursive tests
        $subIterator = new Iterator(
            array(
                'object' => new Object,
                'array' => array(),
                'string' => 'string',
            )
        );
        $iterator = new Iterator(
            array(
                'object' => new Object,
                'iterator' => $subIterator,
                'string' => 'string',
            )
        );
        $proxy = new ProxyObject($iterator, true);
        $expected = array(
            'object' => new ProxyObject(new Object, true),
            'iterator' => new ProxyObject($subIterator, true),
            'string' => new ProxyPrimitive('string', true),
        );
        $actual = iterator_to_array($proxy);

        $this->assertEquals($expected, $actual);

        $actual['iterator'] = iterator_to_array($actual['iterator']);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $actual['object']->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $actual['object']->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $actual['object']->string());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $actual['iterator']['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $actual['iterator']['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $actual['iterator']['string']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $actual['string']);
    }

    public function testIteratorFailure()
    {
        $proxy = new ProxyObject($this->object);

        $this->setExpectedException('LogicException');
        iterator_to_array($proxy);
    }

    public function testToString()
    {
        $stringable = new Stringable;
        $stringable->string = 'foo';
        $proxy = new ProxyObject($stringable);

        $this->assertEquals('foo', strval($proxy));

        // recursive tests
        $stringable = new Stringable;
        $stringable->string = 'foo';
        $proxy = UppercaseProxy::proxyObject($stringable, true);

        $this->assertEquals('FOO', strval($proxy));
    }

    public function testInvoke()
    {
        $callable = new CallableObject;
        $proxy = new ProxyObject($callable);
        $expected = array('__invoke', array('foo', 'bar'));

        $this->assertEquals($expected, $proxy('foo', 'bar'));

        // recursive tests
        $callable = new CallableObject(new Object);
        $proxy = new ProxyObject($callable, true);

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $proxy());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $proxy()->object());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $proxy()->arrayValue());
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $proxy()->string());

        $callable = new CallableObject(
            array(
                'object' => new Object,
                'array' => array(),
                'string' => 'string',
            )
        );
        $proxy = new ProxyObject($callable, true);
        $actual = $proxy();

        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $actual);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyObject', $actual['object']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyArray', $actual['array']);
        $this->assertInstanceOf(__NAMESPACE__ . '\ProxyPrimitive', $actual['string']);

        $callable = new CallableObject('string');
        $proxy = new ProxyObject($callable, true);

        $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy());
    }

    public function testInvokeFailure()
    {
        $proxy = $this->proxy;

        $this->setExpectedException(
            'BadMethodCallException',
            'Call to undefined method '.
                'Eloquent\Pops\Test\Fixture\Object::__invoke()'
        );
        $proxy('foo', 'bar');
    }
}
