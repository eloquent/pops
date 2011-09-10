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

class ProxyArrayTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\Access\ProxyArray::_popsProxySubValueRecursive
   */
  public function testRecursive()
  {
    $array = array(
      'object' => new Object,
      'array' => array(
        'object' => new Object,
        'array' => array(),
        'string' => 'string',
       ),
      'string' => 'string',
    );
    $recursiveProxy = new ProxyArray($array, true);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy['object']->arrayValue());
    $this->assertInstanceOf('Ezzatron\Pops\ProxyPrimitive', $recursiveProxy['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy['array']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $recursiveProxy['array']['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $recursiveProxy['array']['array']);
    $this->assertInstanceOf('Ezzatron\Pops\ProxyPrimitive', $recursiveProxy['array']['string']);
    $this->assertInstanceOf('Ezzatron\Pops\ProxyPrimitive', $recursiveProxy['string']);
  }
}