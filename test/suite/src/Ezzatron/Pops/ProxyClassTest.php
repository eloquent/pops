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

use InvalidArgumentException;
use Ezzatron\Pops\Test\Fixture\Object;
use Ezzatron\Pops\Test\TestCase;

class ProxyClassTest extends TestCase
{
  protected function setUp()
  {
    $this->_class = __NAMESPACE__.'\Test\Fixture\Object';
    $this->_proxy = new ProxyClass($this->_class);
    $this->_recursiveProxy = new ProxyClass($this->_class, true);
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__callStatic
   * @covers Ezzatron\Pops\ProxyClass::_popsProxy
   */
  public function testCallStaticFailure()
  {
    $this->setExpectedException('LogicException');
    ProxyClass::foo();
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   * @covers Ezzatron\Pops\ProxyClass::_popsClass
   */
  public function testConstruct()
  {
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyClass', $this->_proxy);
    $this->assertEquals($this->_class, $this->_proxy->_popsClass());
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   */
  public function testConstructFailureClassType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a string');
    new ProxyClass(1);
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__construct
   */
  public function testConstructFailureRecursiveType()
  {
    $this->setExpectedException('InvalidArgumentException', 'Provided value is not a boolean');
    new ProxyClass($this->_class, 'foo');
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::__call
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValue
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValueRecursive
   */
  public function testCall()
  {
    $this->assertPopsProxyCall($this->_proxy, 'staticPublicMethod', array('foo', 'bar'));
    $this->assertPopsProxyCall($this->_proxy, 'foo', array('bar', 'baz'), true);

    // recursive tests
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticObject());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticObject()->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->staticObject()->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->staticObject()->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->staticArray());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->staticString());
  }
  
  /**
   * @covers Ezzatron\Pops\ProxyClass::__set
   * @covers Ezzatron\Pops\ProxyClass::__get
   * @covers Ezzatron\Pops\ProxyClass::__isset
   * @covers Ezzatron\Pops\ProxyClass::__unset
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValue
   * @covers Ezzatron\Pops\ProxyClass::_popsProxySubValueRecursive
   */
  public function testSetGet()
  {
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);
    
    $this->_proxy->staticPublicProperty = 'foo';
    
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('foo', $this->_proxy->staticPublicProperty);
    
    unset($this->_proxy->staticPublicProperty);
    
    $this->assertFalse(isset($this->_proxy->staticPublicProperty));
    
    $this->_proxy->staticPublicProperty = 'staticPublicProperty';
    
    $this->assertTrue(isset($this->_proxy->staticPublicProperty));
    $this->assertEquals('staticPublicProperty', $this->_proxy->staticPublicProperty);

    // recursive tests
    $staticPublicProperty = Object::$staticPublicProperty;
    Object::$staticPublicProperty = new Object;

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticPublicProperty);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticPublicProperty->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->staticPublicProperty->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->staticPublicProperty->string());

    Object::$staticPublicProperty = array(
      'object' => new Object,
      'array' => array(),
      'string' => 'string',
    );

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->staticPublicProperty);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $this->_recursiveProxy->staticPublicProperty['object']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $this->_recursiveProxy->staticPublicProperty['array']);
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->staticPublicProperty['string']);

    Object::$staticPublicProperty = 'string';

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $this->_recursiveProxy->staticPublicProperty);

    Object::$staticPublicProperty = $staticPublicProperty;
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::_popsGenerateStaticClassProxy
   * @covers Ezzatron\Pops\ProxyClass::_popsStaticClassProxyDefinition
   * @covers Ezzatron\Pops\ProxyClass::_popsStaticClassProxyDefinitionProxyClass
   * @covers Ezzatron\Pops\ProxyClass::_popsStaticClassProxyDefinitionHeader
   * @covers Ezzatron\Pops\ProxyClass::_popsStaticClassProxyDefinitionBody
   * @covers Ezzatron\Pops\ProxyClass::__callStatic
   * @covers Ezzatron\Pops\ProxyClass::_popsProxy
   */
  public function testPopsGenerateStaticClassProxy()
  {
    $class = ProxyClass::_popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object');

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

    // recursive tests
    $class = ProxyClass::_popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object', true);

    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $class::staticObject());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyObject', $class::staticObject()->object());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $class::staticObject()->arrayValue());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $class::staticObject()->string());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyArray', $class::staticArray());
    $this->assertInstanceOf(__NAMESPACE__.'\ProxyPrimitive', $class::staticString());

    // custom class name
    $class = uniqid('Foo');
    ProxyClass::_popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object', null, $class);

    $this->assertTrue(class_exists($class, false));
    $this->assertTrue(is_subclass_of($class, __NAMESPACE__.'\ProxyClass'));
  }

  /**
   * @covers Ezzatron\Pops\ProxyClass::_popsGenerateStaticClassProxy
   */
  public function testPopsGenerateStaticClassProxyFailureRecursiveType()
  {
    $this->setExpectedException('InvalidArgumentException');
    ProxyClass::_popsGenerateStaticClassProxy(__NAMESPACE__.'\Test\Fixture\Object', 'foo');
  }

  /**
   * @var ProxyClass
   */
  protected $_proxy;

  /**
   * @var ProxyClass
   */
  protected $_recursiveProxy;

  /**
   * @var string
   */
  protected $_class;
}