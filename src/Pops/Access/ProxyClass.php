<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops\Access;

use LogicException;
use Pops\ProxyClass as PopsProxyClass;
use ReflectionClass;

class ProxyClass extends PopsProxyClass
{
  /**
   * @param string $class
   */
  public function __construct($class)
  {
    parent::__construct($class);

    $this->_popsReflector = new ReflectionClass($class);
  }
  
  /**
   * @param string $method
   * @param array $arguments
   * 
   * @return mixed
   */
  public function __call($method, array $arguments)
  {
    if (method_exists($this->_popsClass, $method))
    {
      $method = $this->_popsReflector->getMethod($method);
      $method->setAccessible(true);

      return $method->invokeArgs(null, $arguments);
    }

    return parent::__call($method, $arguments);
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function __set($property, $value)
  {
    if ($propertyReflector = $this->_popsPropertyReflector($property))
    {
      $propertyReflector->setValue(null, $value);
      
      return;
    }
    
    throw new LogicException('Access to undeclared static property: '.$this->_popsClass.'::$'.$property);
  }

  /**
   * @param string $property
   * 
   * @return mixed
   */
  public function __get($property)
  {
    if ($propertyReflector = $this->_popsPropertyReflector($property))
    {
      return $propertyReflector->getValue(null);
    }
    
    throw new LogicException('Access to undeclared static property: '.$this->_popsClass.'::$'.$property);
  }

  /**
   * @param string $property
   * 
   * @return boolean
   */
  public function __isset($property)
  {
    if ($propertyReflector = $this->_popsPropertyReflector($property))
    {
      return null !== $propertyReflector->getValue(null);
    }
    
    return parent::__isset($property);
  }

  /**
   * @param string $property
   */
  public function __unset($property)
  {
    if ($propertyReflector = $this->_popsPropertyReflector($property))
    {
      $propertyReflector->setValue(null, null);
      
      return;
    }

    throw new LogicException('Access to undeclared static property: '.$this->_popsClass.'::$'.$property);
  }
  
  /**
   * @param string $property
   * 
   * @return ReflectionProperty|null
   */
  protected function _popsPropertyReflector($property)
  {
    if (property_exists($this->_popsClass, $property))
    {
      $property = $this->_popsReflector->getProperty($property);
      $property->setAccessible(true);
      
      return $property;
    }
    
    return null;
  }

  /**
   * @var ReflectionClass
   */
  protected $_popsReflector;
}