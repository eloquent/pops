<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use InvalidArgumentException;
use Eloquent\Pops\Test\Fixture\ArrayAccess;
use Eloquent\Pops\Test\Fixture\Callable;
use Eloquent\Pops\Test\Fixture\Countable;
use Eloquent\Pops\Test\Fixture\Iterator;
use Eloquent\Pops\Test\Fixture\IteratorAggregate;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\Fixture\Overload;
use Eloquent\Pops\Test\Fixture\Stringable;
use Eloquent\Pops\Test\Fixture\Uppercase\Pops as UppercasePops;
use Eloquent\Pops\Test\TestCase;

class ProxyObjectTest extends TestCase
{
  protected function setUp()
  {
    $this->_object = new Object;
    $this->_proxy = new ProxyObject($this->_object);
    $this->_recursiveProxy = new ProxyObject($this->_object, true);
  }
  
  /**
   * @covers Eloquent\Pops\ProxyObject::__construct
   * @covers Eloquent\Pops\ProxyObject::_popsObject
   */
  public function testConstruct()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_proxy);
    $this->assertSame($this->_object, $this->_proxy->_popsObject());
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__construct
   */
  public function testConstructFailureObjectType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not an object');
    new ProxyObject('foo');
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__construct
   */
  public function testConstructFailureRecursiveType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a boolean');
    new ProxyObject($this->_object, 'foo');
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__call
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
  public function testCall()
  {
    $this->assertPopsProxyCall($this->_proxy, 'publicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);

    // recursive tests
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->object()->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->object()->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->object()->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->string());
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__set
   * @covers Eloquent\Pops\ProxyObject::__get
   * @covers Eloquent\Pops\ProxyObject::__isset
   * @covers Eloquent\Pops\ProxyObject::__unset
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
  public function testSetGet()
  {
    $this->assertEquals('publicProperty', $this->_proxy->publicProperty);

    $this->assertFalse(isset($this->_object->foo));
    $this->assertFalse(isset($this->_proxy->foo));

    $this->_object->foo = 'bar';

    $this->assertTrue(isset($this->_object->foo));
    $this->assertTrue(isset($this->_proxy->foo));
    $this->assertEquals('bar', $this->_object->foo);
    $this->assertEquals('bar', $this->_proxy->foo);

    $this->_proxy->foo = 'baz';

    $this->assertTrue(isset($this->_object->foo));
    $this->assertTrue(isset($this->_proxy->foo));
    $this->assertEquals('baz', $this->_object->foo);
    $this->assertEquals('baz', $this->_proxy->foo);

    unset($this->_object->foo);

    $this->assertFalse(isset($this->_object->foo));
    $this->assertFalse(isset($this->_proxy->foo));

    $this->_proxy->foo = 'qux';

    $this->assertTrue(isset($this->_object->foo));
    $this->assertTrue(isset($this->_proxy->foo));
    $this->assertEquals('qux', $this->_object->foo);
    $this->assertEquals('qux', $this->_proxy->foo);

    unset($this->_proxy->foo);

    $this->assertFalse(isset($this->_object->foo));
    $this->assertFalse(isset($this->_proxy->foo));

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
    $this->_object->publicProperty = new Object;

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->publicProperty);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->publicProperty->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->publicProperty->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->publicProperty->string());

    $this->_object->publicProperty = array(
      'object' => new Object,
      'array' => array(
        'object' => new Object,
        'array' => array(),
        'string' => 'string',
      ),
      'string' => 'string',
    );

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->publicProperty);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->publicProperty['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->publicProperty['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->publicProperty['array']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->publicProperty['array']['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->publicProperty['array']['string']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->publicProperty['string']);

    $this->_object->publicProperty = 'string';

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->publicProperty);

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

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy->object);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy->object->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy->object->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $recursiveProxy->object->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy->array);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy->array['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy->array['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $recursiveProxy->array['string']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $recursiveProxy->string);
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::offsetSet
   * @covers Eloquent\Pops\ProxyObject::offsetGet
   * @covers Eloquent\Pops\ProxyObject::offsetExists
   * @covers Eloquent\Pops\ProxyObject::offsetUnset
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
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

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['object']->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['array']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['array']['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['array']['string']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['string']);
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

  /**
   * @covers Eloquent\Pops\ProxyObject::_popsInnerIterator
   * @covers Eloquent\Pops\ProxyObject::current
   * @covers Eloquent\Pops\ProxyObject::key
   * @covers Eloquent\Pops\ProxyObject::next
   * @covers Eloquent\Pops\ProxyObject::rewind
   * @covers Eloquent\Pops\ProxyObject::valid
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
  public function testIterator()
  {
    $iterator = new Iterator(array(
      'foo' => 'bar',
      'baz' => 'qux',
    ));
    $proxy = new ProxyObject($iterator);

    $this->assertEquals($iterator->values, iterator_to_array($proxy));


    $iteratorAggregate = new IteratorAggregate(array(
      'foo' => 'bar',
      'baz' => 'qux',
    ));
    $proxy = new ProxyObject($iteratorAggregate);

    $this->assertEquals($iteratorAggregate->values, iterator_to_array($proxy));

    // recursive tests
    $sub_iterator = new Iterator(array(
      'object' => new Object,
      'array' => array(),
      'string' => 'string',
    ));
    $iterator = new Iterator(array(
      'object' => new Object,
      'iterator' => $sub_iterator,
      'string' => 'string',
    ));
    $proxy = new ProxyObject($iterator, true);
    $expected = array(
      'object' => new ProxyObject(new Object, true),
      'iterator' => new ProxyObject($sub_iterator, true),
      'string' => new ProxyPrimitive('string', true),
    );
    $actual = iterator_to_array($proxy);

    $this->assertEquals($expected, $actual);

    $actual['iterator'] = iterator_to_array($actual['iterator']);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $actual['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual['object']->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $actual['iterator']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual['iterator']['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['iterator']['string']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['string']);
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::_popsInnerIterator
   */
  public function testIteratorFailure()
  {
    $proxy = new ProxyObject($this->_object);

    $this->setExpectedException('LogicException');
    iterator_to_array($proxy);
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__toString
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
  public function testToString()
  {
    $stringable = new Stringable;
    $stringable->string = 'foo';
    $proxy = new ProxyObject($stringable);

    $this->assertEquals('foo', (string)$proxy);

    // recursive tests
    $stringable = new Stringable;
    $stringable->string = 'foo';
    $proxy = UppercasePops::proxyObject($stringable, true);

    $this->assertEquals('FOO', (string)$proxy);
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__invoke
   * @covers Eloquent\Pops\ProxyObject::_popsProxySubValue
   */
  public function testInvoke()
  {
    $callable = new Callable;
    $proxy = new ProxyObject($callable);
    $expected = array('__invoke', array('foo', 'bar'));

    $this->assertEquals($expected, $proxy('foo', 'bar'));

    // recursive tests
    $callable = new Callable(new Object);
    $proxy = new ProxyObject($callable, true);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy()->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy()->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy()->string());

    $callable = new Callable(array(
      'object' => new Object,
      'array' => array(),
      'string' => 'string',
    ));
    $proxy = new ProxyObject($callable, true);
    $actual = $proxy();

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $actual['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['string']);

    $callable = new Callable('string');
    $proxy = new ProxyObject($callable, true);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy());
  }

  /**
   * @covers Eloquent\Pops\ProxyObject::__invoke
   */
  public function testInvokeFailure()
  {
    $proxy = $this->_proxy;

    $this->setExpectedException('BadMethodCallException', 'Call to undefined method Eloquent\Pops\Test\Fixture\Object::__invoke()');
    $proxy('foo', 'bar');
  }
  
  /**
   * @var ProxyObject
   */
  protected $_proxy;

  /**
   * @var ProxyObject
   */
  protected $_recursiveProxy;

  /**
   * @var Object
   */
  protected $_object;
}