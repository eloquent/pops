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

use Ezzatron\Pops\Test\TestCase;

class ProxyPrimitiveTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\ProxyPrimitive
   */
  public function testPrimitive()
  {
    $proxy = new ProxyPrimitive('foo');

    $this->assertEquals('foo', $proxy->_popsPrimitive());
    $this->assertEquals('foo', (string)$proxy);

    $proxy = new ProxyPrimitive(1);

    $this->assertEquals(1, $proxy->_popsPrimitive());
    $this->assertEquals('1', (string)$proxy);
  }
}