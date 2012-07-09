<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OutputEscaper;

class OutputEscaperProxy extends \Eloquent\Pops\Pops
{
  static protected function proxyArrayClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyArray';
  }

  static protected function proxyClassClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyClass';
  }

  static protected function proxyObjectClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyObject';
  }

  static protected function proxyPrimitiveClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyPrimitive';
  }
}

class OutputEscaperProxyArray extends \Eloquent\Pops\ProxyArray
{
  /**
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

class OutputEscaperProxyClass extends \Eloquent\Pops\ProxyClass
{
  /**
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

class OutputEscaperProxyObject extends \Eloquent\Pops\ProxyObject
{
  /**
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

class OutputEscaperProxyPrimitive extends \Eloquent\Pops\ProxyPrimitive
{
  public function __toString()
  {
    return htmlspecialchars((string)$this->popsPrimitive, ENT_QUOTES, 'UTF-8');
  }
}
