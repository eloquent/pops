<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops\Test;

use ArrayAccess as ArrayAccessInterface;

class ArrayAccess extends Object implements ArrayAccessInterface
{
  /**
   * @param string $property
   * @param mixed $value
   */
  public function offsetSet($property, $value)
  {
    $this->values[$property] = $value;
  }

  /**
   * @param string $property
   * 
   * @return mixed
   */
  public function offsetGet($property)
  {
    return $this->values[$property];
  }

  /**
   * @param string $property
   * 
   * @return boolean
   */
  public function offsetExists($property)
  {
    return array_key_exists($property, $this->values);
  }

  /**
   * @param string $property
   */
  public function offsetUnset($property)
  {
    unset($this->values[$property]);
  }
  
  /**
   * @var array
   */
  protected $values = array();
}