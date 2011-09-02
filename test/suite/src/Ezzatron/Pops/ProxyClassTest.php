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
    $this->_proxy = ProxyClass::proxy($this->_class);
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::proxy
   * @covers Ezzatron\Pops\ProxyClass::__construct
   * @covers Ezzatron\Pops\ProxyClass::_popsClass
   */
  public function testProxy()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyClass', $this->_proxy);
    $this->assertEquals($this->_class, $this->_proxy->_popsClass());
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   */
  public function testConstructFailure()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a string');
    new ProxyClass(1);
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__call
   */
  public function testCall()
  {
    $this->assertPopsProxyCall($this->_proxy, 'staticPublicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__set
   * @covers Ezzatron\Pops\ProxyClass::__get
   * @covers Ezzatron\Pops\ProxyClass::__isset
   * @covers Ezzatron\Pops\ProxyClass::__unset
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