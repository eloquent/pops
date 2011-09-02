<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops\Access;

use Ezzatron\Pops\Test\Fixture\Object;
use Ezzatron\Pops\Test\Fixture\Overload;
use Ezzatron\Pops\Test\TestCase;

class ProxyObjectTest extends TestCase
{
  protected function setUp()
  {
    $this->_object = new Object;
    $this->_proxy = ProxyObject::proxy($this->_object);
  }

  /**
   * @covers Ezzatron\Pops\Access\ProxyObject::__construct
   * @covers Ezzatron\Pops\Access\ProxyObject::__call
   */
  public function testCall()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_proxy);
    
    $this->assertPopsProxyCall($this->_proxy, 'publicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'protectedMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'privateMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);
  }
  
  /**
   * @covers Ezzatron\Pops\Access\ProxyObject::__set
   * @covers Ezzatron\Pops\Access\ProxyObject::__get
   * @covers Ezzatron\Pops\Access\ProxyObject::__isset
   * @covers Ezzatron\Pops\Access\ProxyObject::__unset
   * @covers Ezzatron\Pops\Access\ProxyObject::_popsPropertyReflector
   */
  public function testSetGet()
  {
    $this->assertTrue(isset($this->_proxy->publicProperty));
    $this->assertTrue(isset($this->_proxy->protectedProperty));
    $this->assertTrue(isset($this->_proxy->privateProperty));
    $this->assertEquals('publicProperty', $this->_proxy->publicProperty);
    $this->assertEquals('protectedProperty', $this->_proxy->protectedProperty);
    $this->assertEquals('privateProperty', $this->_proxy->privateProperty);
    
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
    $proxy = ProxyObject::proxy($object);
    
    $this->assertTrue(isset($proxy->foo));
    $this->assertEquals('bar', $proxy->foo);
    
    unset($proxy->foo);
    
    $this->assertFalse(isset($proxy->foo));
    
    $proxy->foo = 'baz';
    
    $this->assertTrue(isset($proxy->foo));
    $this->assertEquals('baz', $proxy->foo);
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