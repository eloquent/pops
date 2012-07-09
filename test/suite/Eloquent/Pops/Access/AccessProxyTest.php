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
use Eloquent\Pops\ProxyPrimitive;

/**
 * @covers Eloquent\Pops\Access\AccessProxy
 * @covers Eloquent\Pops\Pops
 */
class AccessProxyTest extends TestCase
{
  public function testProxy()
  {
    $expected = new AccessProxyClass('Eloquent\Pops\Test\Fixture\Object');

    $this->assertEquals($expected, AccessProxy::proxyClass('Eloquent\Pops\Test\Fixture\Object'));

    $class = AccessProxy::proxyClassStatic('Eloquent\Pops\Test\Fixture\Object');

    $this->assertTrue(class_exists($class));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\AccessProxyClass'));

    $expected = new AccessProxyArray(array());

    $this->assertEquals($expected, AccessProxy::proxy(array()));
    $this->assertEquals($expected, AccessProxy::proxyArray(array()));

    $expected = new AccessProxyObject(new Object);

    $this->assertEquals($expected, AccessProxy::proxy(new Object));
    $this->assertEquals($expected, AccessProxy::proxyObject(new Object));

    $expected = new ProxyPrimitive('string');

    $this->assertEquals($expected, AccessProxy::proxy('string'));
    $this->assertEquals($expected, AccessProxy::proxyPrimitive('string'));
  }
}
