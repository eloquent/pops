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
use Ezzatron\Pops\ProxyPrimitive;

class PopsTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\Access\Pops
   */
  public function testProxy()
  {
    $expected = new ProxyClass('Ezzatron\Pops\Test\Fixture\Object');

    $this->assertEquals($expected, Pops::proxyClass('Ezzatron\Pops\Test\Fixture\Object'));

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