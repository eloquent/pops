<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops;

use Eloquent\Pops\Test\Fixture\Object;
use Eloquent\Pops\Test\Fixture\Uppercase\UppercaseProxy;
use PHPUnit_Framework_TestCase;

/**
 * @covers \Eloquent\Pops\ProxyArray
 * @covers \Eloquent\Pops\AbstractTraversableProxy
 * @covers \Eloquent\Pops\AbstractProxy
 */
class ProxyArrayTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $proxy = new ProxyArray(array('foo', 'bar'));

        $this->assertSame(array('foo', 'bar'), $proxy->popsArray());
    }

    public function testConstructFailureType()
    {
        $this->setExpectedException('Eloquent\Pops\Exception\InvalidTypeException');
        new ProxyArray('foo');
    }

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

        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $proxy['object']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $proxy['object']->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $proxy['object']->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $proxy['object']->string());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $proxy['array']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $proxy['array']['object']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $proxy['array']['object']->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $proxy['array']['array']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $proxy['array']['string']);
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $proxy['string']);
    }

    public function testCount()
    {
        $proxy = new ProxyArray(array_fill(0, 666, null));

        $this->assertEquals(666, count($proxy));
    }

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
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $actual['object']->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $actual['object']->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $actual['object']->string());
        $this->assertInstanceOf('Eloquent\Pops\ProxyObject', $actual['array']['object']->object());
        $this->assertInstanceOf('Eloquent\Pops\ProxyArray', $actual['array']['object']->arrayValue());
        $this->assertInstanceOf('Eloquent\Pops\ProxyPrimitive', $actual['array']['object']->string());
    }

    public function testToString()
    {
        if (version_compare(PHP_VERSION, '5.4.0RC0') >= 0) {
            $error_count = 0;
            set_error_handler(function () use (&$error_count) {
                $error_count ++;
            });
        }

        $proxy = new ProxyArray(array());

        $this->assertEquals('Array', strval($proxy));

        // recursive tests
        $proxy = UppercaseProxy::proxyArray(array(), true);

        $this->assertEquals('ARRAY', strval($proxy));

        if (version_compare(PHP_VERSION, '5.4.0RC0') >= 0) {
            $this->assertSame(2, $error_count);
            restore_error_handler();
        }
    }
}
