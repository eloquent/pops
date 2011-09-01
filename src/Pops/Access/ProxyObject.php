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

use Pops\ProxyObject as PopsProxyObject;
use ReflectionObject;

class ProxyObject extends PopsProxyObject
{
  /**
   * @param object $object
   */
  public function __construct($object)
  {
    parent::__construct($object);

    $this->_popsReflector = new ReflectionObject($object);
  }
  
  /**
   * @param string $method
   * @param array $arguments
   * 
   * @return mixed
   */
  public function __call($method, array $arguments)
  {
    if (method_exists($this->_popsObject, $method))
    {
      $method = $this->_popsReflector->getMethod($method);
      $method->setAccessible(true);

      return $method->invokeArgs($this->_popsObject, $arguments);
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
      $propertyReflector->setValue($this->_popsObject, $value);
      
      return;
    }
    
    parent::__set($property, $value);
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
      return $propertyReflector->getValue($this->_popsObject);
    }
    
    return parent::__get($property);
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
      return null !== $propertyReflector->getValue($this->_popsObject);
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
      $propertyReflector->setValue($this->_popsObject, null);
      
      return;
    }

    parent::__unset($property);
  }
  
  /**
   * @param string $property
   * 
   * @return ReflectionProperty|null
   */
  protected function _popsPropertyReflector($property)
  {
    if (property_exists($this->_popsObject, $property))
    {
      $property = $this->_popsReflector->getProperty($property);
      $property->setAccessible(true);
      
      return $property;
    }
    
    return null;
  }

  /**
   * @var ReflectionObject
   */
  protected $_popsReflector;
}