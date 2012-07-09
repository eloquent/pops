<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Access;

use Eloquent\Pops\Pops;

class AccessProxy extends Pops
{
  /**
   * @return string
   */
  protected static function proxyArrayClass()
  {
    return __NAMESPACE__.'\AccessProxyArray';
  }

  /**
   * @return string
   */
  protected static function proxyClassClass()
  {
    return __NAMESPACE__.'\AccessProxyClass';
  }

  /**
   * @return string
   */
  protected static function proxyObjectClass()
  {
    return __NAMESPACE__.'\AccessProxyObject';
  }
}
