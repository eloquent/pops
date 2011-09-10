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
use Ezzatron\Pops\Test\Fixture\Uppercase\Pops as UppercasePops;
use Ezzatron\Pops\Test\TestCase;

class ProxyArrayTest extends TestCase
{
  /**
   * @covers Ezzatron\Pops\ProxyArray::__construct
   * @covers Ezzatron\Pops\ProxyArray::_popsArray
   */
  public function testConstruct()
  {
    $proxy = new ProxyArray(array('foo', 'bar'));
    $this->assertSame(array('foo', 'bar'), $proxy->_popsArray());
  }

  /**
   * @covers Ezzatron\Pops\ProxyArray::__construct
   */
  public function testConstructFailureRecursiveType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a boolean');
    new ProxyArray(array(), 'foo');
  }

  /**
   * @covers Ezzatron\Pops\ProxyArray::offsetSet
   * @covers Ezzatron\Pops\ProxyArray::offsetGet
   * @covers Ezzatron\Pops\ProxyArray::offsetExists
   * @covers Ezzatron\Pops\ProxyArray::offsetUnset
   * @covers Ezzatron\Pops\ProxyArray::_popsProxySubValue
   */
  public function testOffsetSetGet()
  {
    $proxy = new ProxyArray(array());

    $this->assertFalse(isset($proxy['foo']));

    $proxy['foo'] = null;

    $this->assertFalse(isset($proxy['foo']));

    $proxy['foo'] = 'bar';

    $this->assertTrue(isset($proxy['foo']));
    $this->assertEquals('bar', $proxy['foo']);

    $proxy['foo'] = 'baz';

    $this->assertTrue(isset($proxy['foo']));
    $this->assertEquals('baz', $proxy['foo']);

    unset($proxy['foo']);

    $this->assertFalse(isset($proxy['foo']));

    $proxy['foo'] = 'qux';

    $this->assertTrue(isset($proxy['foo']));
    $this->assertEquals('qux', $proxy['foo']);

    unset($proxy['foo']);

    $this->assertFalse(isset($proxy['foo']));

    // recursive tests
    $array = array(
      'object' => new Object,
      'array' => array(
        'object' => new Object,
        'array' => array(),
        'string' => 'string',
       ),
      'string' => 'string',
    );
    $proxy = new ProxyArray($array, true);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['object']->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['array']['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $proxy['array']['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $proxy['array']['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['array']['string']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $proxy['string']);
  }

  /**
   * @covers Ezzatron\Pops\ProxyArray::count
   */
  public function testCount()
  {
    $proxy = new ProxyArray(array_fill(0, 666, null));

    $this->assertEquals(666, count($proxy));
  }

  /**
   * @covers Ezzatron\Pops\ProxyArray::current
   * @covers Ezzatron\Pops\ProxyArray::key
   * @covers Ezzatron\Pops\ProxyArray::next
   * @covers Ezzatron\Pops\ProxyArray::rewind
   * @covers Ezzatron\Pops\ProxyArray::valid
   * @covers Ezzatron\Pops\ProxyArray::_popsProxySubValue
   */
  public function testIterator()
  {
    $array = array(
      'foo' => 'bar',
      'baz' => 'qux',
    );
    $proxy = new ProxyArray($array);

    $this->assertEquals($array, iterator_to_array($proxy));

    // recursive tests
    $sub_array = array(
      'object' => new Object,
      'array' => array(),
      'string' => 'string',
     );
    $array = array(
      'object' => new Object,
      'array' => $sub_array,
      'string' => 'string',
    );
    $proxy = new ProxyArray($array, true);
    $expected = array(
      'object' => new ProxyObject(new Object, true),
      'array' => new ProxyArray($sub_array, true),
      'string' => new ProxyPrimitive('string', true),
    );
    $actual = iterator_to_array($proxy);

    $this->assertEquals($expected, $actual);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $actual['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual['object']->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['object']->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $actual['array']['object']->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $actual['array']['object']->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $actual['array']['object']->string());
  }

  /**
   * @covers Ezzatron\Pops\ProxyArray::__toString
   * @covers Ezzatron\Pops\ProxyArray::_popsProxySubValue
   */
  public function testToString()
  {
    $proxy = new ProxyArray(array());

    $this->assertEquals('Array', (string)$proxy);

    // recursive tests
    $proxy = UppercasePops::proxyArray(array(), true);
    
    $this->assertEquals('ARRAY', (string)$proxy);
  }
}