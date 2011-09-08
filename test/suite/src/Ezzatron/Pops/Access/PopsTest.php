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

class PopsTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\Access\Pops
   */
  public function testProxy()
  {
    $expected = new ProxyObject(new Object);

    $this->assertEquals($expected, Pops::proxy(new Object));
    $this->assertEquals($expected, Pops::proxyObject(new Object));
  }

  /**
   * @covers Ezzatron\Pops\Access\Pops
   */
  public function testProxyClass()
  {
    $expected = new ProxyClass('Ezzatron\Pops\Test\Fixture\Object');
    $this->assertEquals($expected, Pops::proxyClass('Ezzatron\Pops\Test\Fixture\Object'));
  }

  /**
   * @covers Ezzatron\Pops\Access\Pops
   */
  public function testProxyClassStatic()
  {
    $class = Pops::proxyClassStatic('Ezzatron\Pops\Test\Fixture\Object');

    $this->assertTrue(class_exists($class, false));
    $this->assertTrue(is_subclass_of($class, 'Ezzatron\Pops\Access\ProxyClass'));

    $expected = new $class('Ezzatron\Pops\Test\Fixture\Object');
    $proxy = $class::_popsProxy();

    $this->assertEquals($expected, $proxy);
  }
}