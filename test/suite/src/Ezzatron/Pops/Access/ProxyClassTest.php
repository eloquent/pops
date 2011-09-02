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
use Ezzatron\Pops\Test\TestCase;

class ProxyClassTest extends TestCase
{
  protected function setUp()
  {
    $this->_class = 'Ezzatron\Pops\Test\Fixture\Object';
    $this->_proxy = ProxyClass::proxy($this->_class);
  }
  
  /**
   * @covers Ezzatron\Pops\Access\ProxyClass::__construct
   * @covers Ezzatron\Pops\Access\ProxyClass::__call
   */
  public function testCall()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyClass', $this->_proxy);
    
    $this->assertPopsProxyCall($this->_proxy, 'staticPublicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'staticProtectedMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'staticPrivateMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);
  }
  
  /**
   * @covers Ezzatron\Pops\Access\ProxyClass::__set
   * @covers Ezzatron\Pops\Access\ProxyClass::__get
   * @covers Ezzatron\Pops\Access\ProxyClass::__isset
   * @covers Ezzatron\Pops\Access\ProxyClass::__unset
   * @covers Ezzatron\Pops\Access\ProxyClass::_popsPropertyReflector
   */
  public function testSetGet()
  {
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertTrue(isset($this->_proxy->staticProtectedProperty));
    $this->assertTrue(isset($this->_proxy->staticPrivateProperty));
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);
    $this->assertEquals('staticProtectedProperty', $this->_proxy->staticProtectedProperty);
    $this->assertEquals('staticPrivateProperty', $this->_proxy->staticPrivateProperty);
    
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
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);
    $this->assertEquals('staticProtectedProperty', $this->_proxy->staticProtectedProperty);
    $this->assertEquals('staticPrivateProperty', $this->_proxy->staticPrivateProperty);
    
    $this->assertFalse(isset($this->_proxy->foo));
  }
  
  /**
   * @return array
   */
  public function setGetFailureData()
  {
    return array(
      array('__set', array('foo', 'bar')),
      array('__get', array('foo')),
      array('__unset', array('foo')),
    );
  }
  
  /**
   * @covers Ezzatron\Pops\Access\ProxyClass::__set
   * @covers Ezzatron\Pops\Access\ProxyClass::__get
   * @covers Ezzatron\Pops\Access\ProxyClass::__unset
   * @dataProvider setGetFailureData
   */
  public function testSetGetFailure($method, array $arguments)
  {
    $this->setExpectedException('LogicException', 'Access to undeclared static property: Ezzatron\Pops\Test\Fixture\Object::$'.$arguments[0]);
    call_user_func_array(array($this->_proxy, $method), $arguments);
  }

  /**
   * @var ProxyClass
   */
  protected $_proxy;

  /**
   * @var string
   */
  protected $_class;
}