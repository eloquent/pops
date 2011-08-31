<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops;

use InvalidArgumentException;
use Pops\Test\Fixture\ArrayAccess;
use Pops\Test\Fixture\Callable;
use Pops\Test\Fixture\Countable;
use Pops\Test\Fixture\Object;
use Pops\Test\Fixture\Overload;
use Pops\Test\Fixture\Stringable;
use Pops\Test\Fixture\Traversable;
use Pops\Test\TestCase;

class ProxyObjectTest extends TestCase
{
  protected function setUp()
  {
    $this->_object = new Object;
    $this->_proxy = ProxyObject::proxy($this->_object);
  }
  
  /**
   * @covers Pops\ProxyObject::proxy
   * @covers Pops\ProxyObject::__construct
   * @covers Pops\ProxyObject::_popsObject
   */
  public function testProxy()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_proxy);
    $this->assertSame($this->_object, $this->_proxy->_popsObject());
  }
  
  /**
   * @covers Pops\ProxyObject::__construct
   */
  public function testConstructFailure()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not an object');
    new ProxyObject('foo');
  }
  
  /**
   * @covers Pops\ProxyObject::__call
   */
  public function testCall()
  {
    $this->assertPopsProxyCall($this->_proxy, 'publicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);
  }
  
  /**
   * @covers Pops\ProxyObject::__set
   * @covers Pops\ProxyObject::__get
   * @covers Pops\ProxyObject::__isset
   * @covers Pops\ProxyObject::__unset
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
    $proxy = ProxyObject::proxy($object);
    
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
  }
  
  /**
   * @covers Pops\ProxyObject::offsetSet
   * @covers Pops\ProxyObject::offsetGet
   * @covers Pops\ProxyObject::offsetExists
   * @covers Pops\ProxyObject::offsetUnset
   */
  public function testOffsetSetGet()
  {
    $arrayaccess = new ArrayAccess;
    $proxy = ProxyObject::proxy($arrayaccess);
    
    $this->assertFalse(isset($arrayaccess['foo']));
    $this->assertFalse(isset($proxy['foo']));
    
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
  }
  
  /**
   * @covers Pops\ProxyObject::count
   */
  public function testCount()
  {
    $countable = new Countable;
    $countable->count = 666;
    $proxy = ProxyObject::proxy($countable);

    $this->assertEquals(666, count($proxy));
  }
  
  /**
   * @covers Pops\ProxyObject::getIterator
   */
  public function testGetIterator()
  {
    $traversable = new Traversable;
    $traversable->values = array(
      'foo' => 'bar',
      'baz' => 'qux',
    );
    $proxy = ProxyObject::proxy($traversable);

    $this->assertEquals($traversable->values, iterator_to_array($proxy));
  }
  
  /**
   * @covers Pops\ProxyObject::__toString
   */
  public function testToString()
  {
    $stringable = new Stringable;
    $stringable->string = 'foo';
    $proxy = ProxyObject::proxy($stringable);
    
    $this->assertEquals('foo', (string)$proxy);
  }

  /**
   * @covers Pops\ProxyObject::__invoke
   */
  public function testInvoke()
  {
    $callable = new Callable;
    $proxy = ProxyObject::proxy($callable);
    $expected = array('__invoke', array('foo', 'bar'));
    
    $this->assertEquals($expected, $proxy('foo', 'bar'));
  }
  
  /**
   * @covers Pops\ProxyObject::__invoke
   */
  public function testInvokeFailure()
  {
    $proxy = $this->_proxy;
    
    $this->setExpectedException('BadMethodCallException', 'Call to undefined method Pops\Test\Fixture\Object::__invoke()');
    $proxy('foo', 'bar');
  }
  
  /**
   * @var ProxyObject
   */
  protected $_proxy;

  /**
   * @var Object
   */
  protected $_object;
}