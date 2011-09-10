<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops;

use InvalidArgumentException;
use Ezzatron\Pops\Test\Fixture\Object;
use Ezzatron\Pops\Test\TestCase;

class ProxyClassTest extends TestCase
{
  protected function setUp()
  {
    $this->_class = __NAMESPACE__.'\Test\Fixture\Object';
    $this->_proxy = new ProxyClass($this->_class);
    $this->_recursiveProxy = new ProxyClass($this->_class, true);
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__callStatic
   * @covers Ezzatron\Pops\ProxyClass::_popsProxy
   */
  public function testCallStaticFailure()
  {
    $this->setExpectedException('LogicException');
    ProxyClass::foo();
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   * @covers Ezzatron\Pops\ProxyClass::_popsClass
   */
  public function testConstruct()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyClass', $this->_proxy);
    $this->assertEquals($this->_class, $this->_proxy->_popsClass());
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   */
  public function testConstructFailureClassType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a string');
    new ProxyClass(1);
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   */
  public function testConstructFailureRecursiveType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a boolean');
    new ProxyClass($this->_class, 'foo');
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__call
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValue
   */
  public function testCall()
  {
    $this->assertPopsProxyCall($this->_proxy, 'staticPublicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);

    // recursive tests
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticObject());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticObject()->object());
    $this->assertEquals('string', $this->_recursiveProxy->staticString());
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__set
   * @covers Ezzatron\Pops\ProxyClass::__get
   * @covers Ezzatron\Pops\ProxyClass::__isset
   * @covers Ezzatron\Pops\ProxyClass::__unset
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValue
   */
  public function testSetGet()
  {
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);
    
    $this->_proxy->staticPublicProperty = 'foo';
    
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('foo', $this->_proxy->staticPublicProperty);
    
    unset($this->_proxy->staticPublicProperty);
    
    $this->assertFalse(isset($this->_proxy->staticPublicProperty));
    
    $this->_proxy->staticPublicProperty = 'staticPublicProperty';
    
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);

    // recursive tests
    $staticPublicProperty = Object::$staticPublicProperty;
    Object::$staticPublicProperty = new Object;

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticPublicProperty);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticPublicProperty->object());

    Object::$staticPublicProperty = 'string';

    $this->assertEquals('string', $this->_recursiveProxy->staticPublicProperty);

    Object::$staticPublicProperty = $staticPublicProperty;
  }

  /**
   * @var ProxyClass
   */
  protected $_proxy;

  /**
   * @var ProxyClass
   */
  protected $_recursiveProxy;

  /**
   * @var string
   */
  protected $_class;
}