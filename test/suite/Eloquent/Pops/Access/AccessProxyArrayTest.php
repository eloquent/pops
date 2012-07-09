<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Access;

use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\TestCase;

/**
 * @covers Eloquent\Pops\Access\AccessProxyArray
 * @covers Eloquent\Pops\ProxyArray
 */
class AccessProxyArrayTest extends TestCase
{
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
    $recursiveProxy = new AccessProxyArray($array, true);

    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyObject', $recursiveProxy['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyObject', $recursiveProxy['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyArray', $recursiveProxy['object']->arrayValue());
    $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $recursiveProxy['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyArray', $recursiveProxy['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyObject', $recursiveProxy['array']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyObject', $recursiveProxy['array']['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\AccessProxyArray', $recursiveProxy['array']['array']);
    $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $recursiveProxy['array']['string']);
    $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $recursiveProxy['string']);
  }
}
