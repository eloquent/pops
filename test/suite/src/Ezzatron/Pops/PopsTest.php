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
   */
  public function testProxy()
  {
    $expected = new ProxyObject(new Object);

    $this->assertEquals($expected, Pops::proxy(new Object));
    $this->assertEquals($expected, Pops::proxyObject(new Object));
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
   * @covers Ezzatron\Pops\Pops::proxyClassStaticDefinition
   * @covers Ezzatron\Pops\Pops::proxyClassStaticProxyClass
   * @covers Ezzatron\Pops\Pops::proxyClassStaticDefinitionHeader
   * @covers Ezzatron\Pops\Pops::proxyClassStaticDefinitionBody
   * @covers Ezzatron\Pops\ProxyClass::__callStatic
   * @covers Ezzatron\Pops\ProxyClass::_popsProxy
   */
  public function testProxyClassStatic()
  {
    $class = Pops::proxyClassStatic(__NAMESPACE__.'\Test\Fixture\Object');

    $this->assertTrue(class_exists($class, false));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));

    $expected = new $class(__NAMESPACE__.'\Test\Fixture\Object');
    $proxy = $class::_popsProxy();

    $this->assertEquals($expected, $proxy);
    $this->assertSame($proxy, $class::_popsProxy());

    $this->assertEquals(
      array('staticPublicMethod', array('foo', 'bar'))
      , $class::staticPublicMethod('foo', 'bar')
    );
    $this->assertEquals(
      array('__callStatic', array('foo', array('bar', 'baz')))
      , $class::foo('bar', 'baz')
    );


    $className = uniqid('Foo');
    $class = Pops::proxyClassStatic(__NAMESPACE__.'\Test\Fixture\Object', $className);

    $this->assertTrue(class_exists($class, false));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
  }
}