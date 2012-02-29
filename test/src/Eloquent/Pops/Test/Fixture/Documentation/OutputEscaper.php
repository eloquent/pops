<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OutputEscaper;

class Pops extends \Eloquent\Pops\Pops {}
class ProxyArray extends \Eloquent\Pops\ProxyArray {}
class ProxyClass extends \Eloquent\Pops\ProxyClass {}
class ProxyObject extends \Eloquent\Pops\ProxyObject {}

class ProxyPrimitive extends \Eloquent\Pops\ProxyPrimitive
{
  public function __toString()
  {
    return htmlspecialchars((string)$this->_popsPrimitive, ENT_QUOTES, 'UTF-8');
  }
}