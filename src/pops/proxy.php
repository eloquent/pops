<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;

class Proxy implements ArrayAccess, Countable, IteratorAggregate
{
  /**
   * @param object $object
   * 
   * @return Proxy
   */
  static public function proxy($object)
  {
    return new static($object);
  }

  /**
   * @param object $object
   */
  public function __construct($object)
  {
    if (!is_object($object))
    {
      throw new InvalidArgumentException('Provided value is not an object.');
    }

    $this->_popsObject = $object;
  }

  /**
   * @return object
   */
  public function _popsObject()
  {
    return $this->_popsObject;
  }

  /**
   * @param string $method
   * @param array $arguments
   * 
   * @return mixed
   */
  public function __call($method, array $arguments)
  {
    return call_user_func_array(array($this->_popsObject, $method), $arguments);
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function __set($property, $value)
  {
    $this->_popsObject->$property = $value;
  }

  /**
   * @param string $property
   * 
   * @return mixed
   */
  public function __get($property)
  {
    return $this->_popsObject->$property;
  }

  /**
   * @param string $property
   * 
   * @return boolean
   */
  public function __isset($property)
  {
    return isset($this->_popsObject->$property);
  }

  /**
   * @param string $property
   */
  public function __unset($property)
  {
    unset($this->_popsObject->$property);
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function offsetSet($property, $value)
  {
    $this->__call('offsetSet', func_get_args());
  }

  /**
   * @param string $property
   * 
   * @return mixed
   */
  public function offsetGet($property)
  {
    return $this->__call('offsetGet', func_get_args());
  }

  /**
   * @param string $property
   * 
   * @return boolean
   */
  public function offsetExists($property)
  {
    return $this->__call('offsetExists', func_get_args());
  }

  /**
   * @param string $property
   */
  public function offsetUnset($property)
  {
    $this->__call('offsetUnset', func_get_args());
  }

  /**
   * @return integer
   */
  public function count()
  {
    return $this->__call('count', array());
  }

  /**
   * @return Iterator
   */
  public function getIterator()
  {
    return $this->_popsObject;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return $this->__call('__toString', array());
  }

  /**
   * @return mixed
   */
  public function __invoke()
  {
    if (!method_exists($this->_popsObject, '__invoke'))
    {
      throw new BadMethodCallException('Call to undefined method '.get_class($this->_popsObject).'::__invoke()');
    }
    
    return call_user_func_array($this->_popsObject, func_get_args());
  }

  /**
   * @var object
   */
  protected $_popsObject;
}