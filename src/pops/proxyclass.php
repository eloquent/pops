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

use InvalidArgumentException;

class ProxyClass
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
   * @var string
   */
  protected $_popsClass;
}
