<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use ArrayAccess;
use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use LogicException;
use ReflectionClass;

class ProxyObject implements Proxy, ArrayAccess, Countable, Iterator
{
  /**
   * @param object $object
   * @param boolean $recursive
   */
  public function __construct($object, $recursive = null)
  {
    if (!is_object($object))
    {
      throw new InvalidArgumentException('Provided value is not an object');
    }

    if (null === $recursive)
    {
      $recursive = false;
    }
    if (!is_bool($recursive))
    {
      throw new InvalidArgumentException('Provided value is not a boolean');
    }

    $this->_popsObject = $object;
    $this->_popsRecursive = $recursive;
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
    return $this->_popsProxySubValue(
      call_user_func_array(array($this->_popsObject, $method), $arguments)
    );
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
    return $this->_popsProxySubValue(
      $this->_popsObject->$property
    );
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
   * @return mixed
   */
  public function current()
  {
    return $this->_popsProxySubValue(
      $this->_popsInnerIterator()->current()
    );
  }

  /**
   * @return scalar
   */
  public function key()
  {
    return $this->_popsInnerIterator()->key();
  }

  public function next()
  {
    $this->_popsInnerIterator()->next();
  }

  public function rewind()
  {
    $this->_popsInnerIterator()->rewind();
  }

  /**
   * @return boolean
   */
  public function valid()
  {
    return $this->_popsInnerIterator()->valid();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)$this->__call('__toString', array());
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
    
    return $this->_popsProxySubValue(
      call_user_func_array($this->_popsObject, func_get_args())
    );
  }

  /**
   * @return Iterator
   */
  protected function _popsInnerIterator()
  {
    if (null !== $this->_popsInnerIterator)
    {
      return $this->_popsInnerIterator;
    }

    if ($this->_popsObject instanceof Iterator)
    {
      $this->_popsInnerIterator = $this->_popsObject;
    }
    else if ($this->_popsObject instanceof IteratorAggregate)
    {
      $this->_popsInnerIterator = $this->_popsObject->getIterator();
    }
    else
    {
      throw new LogicException('Proxied object is not an instance of Traversable');
    }

    return $this->_popsInnerIterator;
  }

  /**
   * @param mixed $value
   *
   * @return mixed
   */
  protected function _popsProxySubValue($value)
  {
    if ($this->_popsRecursive)
    {
      $class = new ReflectionClass(get_called_class());
      $namespace = $class->getNamespaceName();
      $popsClass = $namespace.'\Pops';

      return $popsClass::proxy($value, true);
    }

    return $value;
  }

  /**
   * @var object
   */
  protected $_popsObject;

  /**
   * @var boolean
   */
  protected $_popsRecursive;

  /**
   * @var Iterator
   */
  protected $_popsInnerIterator;
}