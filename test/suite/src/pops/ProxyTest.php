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
use Phake;
use PHPUnit_Framework_TestCase;
use Pops\Test\Object;

class ProxyTest extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this->_object = Phake::partialMock(__NAMESPACE__.'\Test\Object');
    $this->_proxy = Proxy::proxy($this->_object);
  }
  
  /**
   * @covers Pops\Proxy::proxy
   * @covers Pops\Proxy::__construct
   * @covers Pops\Proxy::_popsObject
   */
  public function testProxy()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\Proxy', $this->_proxy);
    $this->assertSame($this->_object, $this->_proxy->_popsObject());
  }
  
  /**
   * @covers Pops\Proxy::__construct
   */
  public function testConstructFailure()
  {
    $this->setExpectedException('InvalidArgumentException');
    new Proxy('foo');
  }
  
  /**
   * @covers Pops\Proxy::__call
   */
  public function testCall()
  {
    $this->assertEquals('publicMethod', $this->_proxy->publicMethod('foo', 'bar'));
    
    Phake::verify($this->_object)->publicMethod('foo', 'bar');
  }
  
  /**
   * @covers Pops\Proxy::__set
   * @covers Pops\Proxy::__get
   * @covers Pops\Proxy::__isset
   * @covers Pops\Proxy::__unset
   */
  public function testSetGet()
  {
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
  }
  
  /**
   * @covers Pops\Proxy::offsetSet
   * @covers Pops\Proxy::offsetGet
   * @covers Pops\Proxy::offsetExists
   * @covers Pops\Proxy::offsetUnset
   */
  public function testOffsetSetGet()
  {
    $arrayaccess = Phake::partialMock(__NAMESPACE__.'\Test\ArrayAccess');
    $proxy = Proxy::proxy($arrayaccess);
    
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
   * @covers Pops\Proxy::count
   */
  public function testCount()
  {
    $countable = Phake::partialMock(__NAMESPACE__.'\Test\Countable');
    $proxy = Proxy::proxy($countable);

    $this->assertEquals(666, count($proxy));
  }
  
  /**
   * @covers Pops\Proxy::getIterator
   */
  public function testGetIterator()
  {
    $traversable = Phake::partialMock(__NAMESPACE__.'\Test\Traversable');
    $proxy = Proxy::proxy($traversable);
    $expected = array(
      'foo' => 'bar',
      'baz' => 'qux',
    );

    $this->assertEquals($expected, iterator_to_array($proxy));
  }
  
  /**
   * @covers Pops\Proxy::__toString
   */
  public function testToString()
  {
    $stringable = Phake::partialMock(__NAMESPACE__.'\Test\Stringable');
    Phake::when($stringable)->__toString()->thenCallParent();
    $proxy = Proxy::proxy($stringable);
    
    $this->assertEquals('__toString', (string)$proxy);
    
    Phake::verify($stringable)->__toString();
    $this->assertTrue(true);
  }

  /**
   * @covers Pops\Proxy::__invoke
   */
  public function testInvoke()
  {
    $callable = Phake::partialMock(__NAMESPACE__.'\Test\Callable');
    $proxy = Proxy::proxy($callable);
    
    $this->assertEquals('__invoke', $proxy('foo', 'bar'));
    
    Phake::verify($callable)->__invoke('foo', 'bar');
    $this->assertTrue(true);
  }
  
  /**
   * @covers Pops\Proxy::__invoke
   */
  public function testInvokeFailure()
  {
    $proxy = $this->_proxy;
    
    $this->setExpectedException('BadMethodCallException');
    $proxy('foo', 'bar');
  }
  
  /**
   * @var Proxy
   */
  protected $_proxy;

  /**
   * @var Object
   */
  protected $_object;
}
