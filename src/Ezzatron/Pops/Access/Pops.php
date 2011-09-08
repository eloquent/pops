<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops\Access;

use Ezzatron\Pops\Pops as BasePops;

class Pops extends BasePops
{
  /**
   * @return string
   */
  static protected function proxyObjectClass()
  {
    return __NAMESPACE__.'\ProxyObject';
  }

  /**
   * @return string
   */
  static protected function proxyClassClass()
  {
    return __NAMESPACE__.'\ProxyClass';
  }
}