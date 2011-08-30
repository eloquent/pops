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

class ProxyClassTest extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this->_class = __NAMESPACE__.'\Test\Object';
    $this->_proxy = ProxyClass::proxy($this->_class);
  }
  
  /**
   * @covers Pops\ProxyClass::proxy
   * @covers Pops\ProxyClass::__construct
   * @covers Pops\ProxyClass::_popsClass
   */
  public function testProxy()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyClass', $this->_proxy);
    $this->assertEquals($this->_class, $this->_proxy->_popsClass());
  }
  
  /**
   * @covers Pops\ProxyClass::__construct
   */
  public function testConstructFailure()
  {
    $this->setExpectedException('InvalidArgumentException');
    new ProxyClass(1);
  }
  
  /**
   * @covers Pops\ProxyClass::__call
   */
  public function testCall()
  {
    $this->assertEquals('staticPublicMethod', $this->_proxy->staticPublicMethod());
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
