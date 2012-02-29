<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Access;

use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;
use Eloquent\Pops\ProxyPrimitive;

class PopsTest extends TestCase
{
  /**
   * @covers Eloquent\Pops\Pops
   */
  public function testProxy()
  {
    $expected = new ProxyClass('Eloquent\Pops\Test\Fixture\Object');

    $this->assertEquals($expected, Pops::proxyClass('Eloquent\Pops\Test\Fixture\Object'));

    $class = Pops::proxyClassStatic('Eloquent\Pops\Test\Fixture\Object');

    $this->assertTrue(class_exists($class));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));

    $expected = new ProxyArray(array());

    $this->assertEquals($expected, Pops::proxy(array()));
    $this->assertEquals($expected, Pops::proxyArray(array()));

    $expected = new ProxyObject(new Object);

    $this->assertEquals($expected, Pops::proxy(new Object));
    $this->assertEquals($expected, Pops::proxyObject(new Object));

    $expected = new ProxyPrimitive('string');

    $this->assertEquals($expected, Pops::proxy('string'));
    $this->assertEquals($expected, Pops::proxyPrimitive('string'));
  }
}