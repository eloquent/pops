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

use Ezzatron\Pops\ProxyPrimitive as PopsProxyPrimitive;

class ProxyPrimitive extends PopsProxyPrimitive
{
  /**
   * @return string
   */
  public function __toString()
  {
    return mb_strtoupper((string)$this->_popsPrimitive);
  }
}