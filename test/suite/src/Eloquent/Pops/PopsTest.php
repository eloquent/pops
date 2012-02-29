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

use Eloquent\Pops\Safe\Pops as Safe;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;

class PopsTest extends TestCase
{
  /**
   * @covers Eloquent\Pops\Pops::proxy
   * @covers Eloquent\Pops\Pops::proxyArray
   * @covers Eloquent\Pops\Pops::proxyArrayClass
   * @covers Eloquent\Pops\Pops::proxyObject
   * @covers Eloquent\Pops\Pops::proxyObjectClass
   * @covers Eloquent\Pops\Pops::proxyPrimitive
   * @covers Eloquent\Pops\Pops::proxyPrimitiveClass
   * @covers Eloquent\Pops\Pops::proxyDynamicClassSelect
   */
  public function testProxy()
  {
    $safe = Safe::proxy(new Object, true);

    $this->assertEquals($safe, Pops::proxy($safe));
    $this->assertEquals($safe->object(), Pops::proxy($safe->object()));
    $this->assertEquals($safe->object(), Pops::proxy($safe)->object());

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
   * @covers Eloquent\Pops\Pops::proxyClass
   * @covers Eloquent\Pops\Pops::proxyClassClass
   */
  public function testProxyClass()
  {
    $expected = new ProxyClass(__NAMESPACE__.'\Test\Fixture\Object');
    $this->assertEquals($expected, Pops::proxyClass(__NAMESPACE__.'\Test\Fixture\Object'));
  }

  /**
   * @covers Eloquent\Pops\Pops::proxyClassStatic
   */
  public function testProxyClassStatic()
  {
    $class = Pops::proxyClassStatic(__NAMESPACE__.'\Test\Fixture\Object');

    $this->assertTrue(class_exists($class));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
  }
}