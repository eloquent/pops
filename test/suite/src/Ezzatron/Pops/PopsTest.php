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

use Ezzatron\Pops\Test\Fixture\Object;
use Ezzatron\Pops\Test\TestCase;

class PopsTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\Pops::proxy
   * @covers Ezzatron\Pops\Pops::proxyObject
   * @covers Ezzatron\Pops\Pops::proxyObjectClass
   * @covers Ezzatron\Pops\Pops::proxyPrimitive
   * @covers Ezzatron\Pops\Pops::proxyPrimitiveClass
   */
  public function testProxy()
  {
    $expected = new ProxyObject(new Object);

    $this->assertEquals($expected, Pops::proxy(new Object));
    $this->assertEquals($expected, Pops::proxyObject(new Object));

    $expected = new ProxyArray(array());

    $this->assertEquals($expected, Pops::proxy(array()));
    $this->assertEquals($expected, Pops::proxyArray(array()));

    $expected = new ProxyPrimitive('string');

    $this->assertEquals($expected, Pops::proxy('string'));
    $this->assertEquals($expected, Pops::proxyPrimitive('string'));
  }

  /**
   * @covers Ezzatron\Pops\Pops::proxyClass
   * @covers Ezzatron\Pops\Pops::proxyClassClass
   */
  public function testProxyClass()
  {
    $expected = new ProxyClass(__NAMESPACE__.'\Test\Fixture\Object');
    $this->assertEquals($expected, Pops::proxyClass(__NAMESPACE__.'\Test\Fixture\Object'));
  }

  /**
   * @covers Ezzatron\Pops\Pops::proxyClassStatic
   */
  public function testProxyClassStatic()
  {
    $class = Pops::proxyClassStatic(__NAMESPACE__.'\Test\Fixture\Object');

    $this->assertTrue(class_exists($class));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
  }
}