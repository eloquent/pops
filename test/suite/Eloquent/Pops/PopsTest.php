<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use Eloquent\Pops\Safe\SafeProxy;
use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Pops
 * @covers Eloquent\Pops\Safe\SafeProxy
 * @covers Eloquent\Pops\Safe\SafeProxyArray
 * @covers Eloquent\Pops\Safe\SafeProxyClass
 * @covers Eloquent\Pops\Safe\SafeProxyObject
 * @covers Eloquent\Pops\Safe\SafeProxyPrimitive
 */
class PopsTest extends TestCase
{
  public function testProxy()
  {
    $safe = SafeProxy::proxy(new Object, true);

    $this->assertSame($safe, Pops::proxy($safe));
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

  public function testProxyClass()
  {
    $expected = new ProxyClass(__NAMESPACE__.'\Test\Fixture\Object');
    $this->assertEquals($expected, Pops::proxyClass(__NAMESPACE__.'\Test\Fixture\Object'));
  }

  public function testProxyClassStatic()
  {
    $class = Pops::proxyClassStatic(__NAMESPACE__.'\Test\Fixture\Object');

    $this->assertTrue(class_exists($class));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
  }
}
