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

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use Iterator;

class ProxyArray implements Proxy, ArrayAccess, Countable, Iterator
{
  /**
   * @param array $array
   * @param boolean $recursive
   */
  public function __construct(array $array, $recursive = null)
  {
    if (null === $recursive)
    {
      $recursive = false;
    }
    if (!is_bool($recursive))
    {
      throw new InvalidArgumentException('Provided value is not a boolean');
    }

    $this->_popsArray = $array;
    $this->_popsRecursive = $recursive;
    $this->_popsInnerIterator = new ArrayIterator($this->_popsArray);
  }

  /**
   * @return array
   */
  public function _popsArray()
  {
    return $this->_popsArray;
  }

  /**
   * @param string $property
   * @param mixed $value
   */
  public function offsetSet($property, $value)
  {
    $this->_popsArray[$property] = $value;
  }

  /**
   * @param string $property
   *
   * @return mixed
   */
  public function offsetGet($property)
  {
    return $this->_popsProxySubValue(
      $this->_popsArray[$property]
    );
  }

  /**
   * @param string $property
   *
   * @return boolean
   */
  public function offsetExists($property)
  {
    return isset($this->_popsArray[$property]);
  }

  /**
   * @param string $property
   */
  public function offsetUnset($property)
  {
    unset($this->_popsArray[$property]);
  }

  /**
   * @return integer
   */
  public function count()
  {
    return count($this->_popsArray);
  }

  /**
   * @return mixed
   */
  public function current()
  {
    return $this->_popsProxySubValue(
      $this->_popsInnerIterator->current()
    );
  }

  /**
   * @return scalar
   */
  public function key()
  {
    return $this->_popsInnerIterator->key();
  }

  public function next()
  {
    $this->_popsInnerIterator->next();
  }

  public function rewind()
  {
    $this->_popsInnerIterator->rewind();
  }

  /**
   * @return boolean
   */
  public function valid()
  {
    return $this->_popsInnerIterator->valid();
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)$this->_popsProxySubValue(
      (string)$this->_popsArray
    );
  }

  /**
   * @param mixed $value
   *
   * @return Proxy
   */
  static protected function _popsProxySubValueRecursive($value)
  {
    return Pops::proxy($value, true);
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
      return static::_popsProxySubValueRecursive($value);
    }

    return $value;
  }

  /**
   * @var array
   */
  protected $_popsArray;

  /**
   * @var boolean
   */
  protected $_popsRecursive;

  /**
   * @var Iterator
   */
  protected $_popsInnerIterator;
}