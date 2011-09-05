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

class ProxyClass implements Proxy
{
  /**
   * @param string $class
   * 
   * @return ProxyClass
   */
  static public function proxy($class)
  {
    return new static($class);
  }

  /**
   * @param string $class
   * @param string $proxyClass
   *
   * @return string
   */
  static public function proxyClass($class, $proxyClass = null)
  {
    $classDef = static::proxyClassDefinition($class, $proxyClass);
    eval($classDef);

    return $proxyClass;
  }

  /**
   * @param string $class
   */
  public function __construct($class)
  {
    if (!is_string($class))
    {
      throw new InvalidArgumentException('Provided value is not a string');
    }

    $this->_popsClass = $class;
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
    return call_user_func_array($this->_popsClass . '::' . $method, $arguments);
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
    
    return $class::$$property;
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
   * @param string $class
   * @param string $proxyClass
   * @param string $proxyClassClass
   * @param string $proxyClassBaseClass
   *
   * @return string
   */
  static protected function proxyClassDefinition($class, &$proxyClass, $proxyClassClass = null, $proxyClassBaseClass = null)
  {
    if (null === $proxyClass)
    {
      $classParts = explode('\\', $class);
      $proxyClassPrefix = array_pop($classParts).'_Pops_';
      $proxyClass = uniqid($proxyClassPrefix);
    }
    if (null === $proxyClassClass)
    {
      $proxyClassClass = __CLASS__;
    }
    if (null === $proxyClassBaseClass)
    {
      $proxyClassBaseClass = __NAMESPACE__.'\ProxyClassBase';
    }

    $classDef = 'class '.$proxyClass.' extends '.$proxyClassBaseClass.' { ';
    $classDef .= ' static protected $_popsProxyClassClass = '.var_export($proxyClassClass, true).';';
    $classDef .= ' static protected $_popsClass = '.var_export($class, true).';';
    $classDef .= ' }';

    return $classDef;
  }

  /**
   * @var string
   */
  protected $_popsClass;
}