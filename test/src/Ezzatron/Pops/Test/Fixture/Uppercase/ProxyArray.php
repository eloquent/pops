<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops\Test\Fixture\Uppercase;

use Ezzatron\Pops\ProxyArray as PopsProxyArray;

class ProxyArray extends PopsProxyArray
{
  /**
   * @param mixed $value
   *
   * @return Proxy
   */
  static protected function _popsProxySubValueRecursive($value)
  {
    return Pops::proxy($value, true);
  }
}