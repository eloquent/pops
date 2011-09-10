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
use LogicException;

class ProxyClass implements Proxy
{
  /**
   * @param string $method
   * @param array $arguments
   *
   * @return mixed
   */
  static public function __callStatic($method, array $arguments)
  {
    return static::_popsProxySubValue(
      call_user_func_array(array(static::_popsProxy(), $method), $arguments)
      , static::$_popsStaticRecursive
    );
  }

  /**
   * @return ProxyClass
   */
  static public function _popsProxy()
  {
    $originalClass = static::$_popsStaticOriginalClass;
    if (null === $originalClass)
    {
      throw new LogicException('This class should not be called directly.');
    }

    $proxyClassClass = get_called_class();

    if (!isset(self::$_popsStaticProxies[$proxyClassClass][$originalClass]))
    {
      self::$_popsStaticProxies[$proxyClassClass][$originalClass] = new $proxyClassClass($originalClass);
    }

    return self::$_popsStaticProxies[$proxyClassClass][$originalClass];
  }

  /**
   * @param string $class
   * @param boolean $recursive
   */
  public function __construct($class, $recursive = null)
  {
    if (!is_string($class))
    {
      throw new InvalidArgumentException('Provided value is not a string');
    }

    if (null === $recursive)
    {
      $recursive = false;
    }
    if (!is_bool($recursive))
    {
      throw new InvalidArgumentException('Provided value is not a boolean');
    }

    $this->_popsClass = $class;
    $this->_popsRecursive = $recursive;
  }

  /**
   * @return string
   */
  public function _popsClass()
  {
    return $this->_popsClass;
  }

  /**
   * @param string $method
   * @param array $arguments
   * 
   * @return mixed
   */
  public function __call($method, array $arguments)
  {
    return static::_popsProxySubValue(
      call_user_func_array($this->_popsClass . '::' . $method, $arguments)
      , $this->_popsRecursive
    );
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function __set($property, $value)
  {
    $class = $this->_popsClass;
    $class::$$property = $value;
  }

  /**
   * @param string $property
   * 
   * @return mixed
   */
  public function __get($property)
  {
    $class = $this->_popsClass;

    return static::_popsProxySubValue(
        $class::$$property
      , $this->_popsRecursive
    );
  }

  /**
   * @param string $property
   * 
   * @return boolean
   */
  public function __isset($property)
  {
    $class = $this->_popsClass;
    
    return isset($class::$$property);
  }

  /**
   * @param string $property
   */
  public function __unset($property)
  {
    $class = $this->_popsClass;
    
    $class::$$property = null;
  }

  /**
   * @param mixed $value
   * @param boolean $recursive
   *
   * @return mixed
   */
  static protected function _popsProxySubValue($value, $recursive)
  {
    if ($recursive)
    {
      return Pops::proxy($value, true);
    }

    return $value;
  }

  /**
   * @var string
   */
  static protected $_popsStaticOriginalClass;

  /**
   * @var string
   */
  static protected $_popsStaticRecursive;

  /**
   * @var array
   */
  static protected $_popsStaticProxies = array();

  /**
   * @var string
   */
  protected $_popsClass;

  /**
   * @var boolean
   */
  protected $_popsRecursive;
}