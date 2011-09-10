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

class ProxyPrimitive
{
  /**
   * @param mixed $primitive
   */
  public function __construct($primitive)
  {
    $this->_popsPrimitive = $primitive;
  }

  /**
   * @return object
   */
  public function _popsPrimitive()
  {
    return $this->_popsPrimitive;
  }

  /**
   * @return string
   */
  public function __toString()
  {
    return (string)$this->_popsPrimitive;
  }

  /**
   * @var mixed
   */
  protected $_popsPrimitive;
}