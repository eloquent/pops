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

/**
 * Escapes output for use in HTML.
 */
class OutputEscaperProxy extends \Eloquent\Pops\Pops
{
  /**
   * The class to use when proxying arrays.
   *
   * @return string
   */
  static protected function proxyArrayClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyArray';
  }

  /**
   * The class to use when proxying classes.
   *
   * @return string
   */
  static protected function proxyClassClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyClass';
  }

  /**
   * The class to use when proxying objects.
   *
   * @return string
   */
  static protected function proxyObjectClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyObject';
  }

  /**
   * The class to use when proxying primitives.
   *
   * @return string
   */
  static protected function proxyPrimitiveClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxyPrimitive';
  }
}

/**
 * Wraps an array to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyArray extends \Eloquent\Pops\ProxyArray
{
  /**
   * The class to use when proxying sub-values.
   *
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

/**
 * Wraps a class to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyClass extends \Eloquent\Pops\ProxyClass
{
  /**
   * The class to use when proxying sub-values.
   *
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

/**
 * Wraps an object to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyObject extends \Eloquent\Pops\ProxyObject
{
  /**
   * The class to use when proxying sub-values.
   *
   * @return string
   */
  protected static function popsProxyClass()
  {
    return __NAMESPACE__.'\OutputEscaperProxy';
  }
}

/**
 * Wraps a primitive to escape its value for use in HTML.
 */
class OutputEscaperProxyPrimitive extends \Eloquent\Pops\ProxyPrimitive
{
  /**
   * Returns the HTML-escaped version of this primitive.
   *
   * @return string
   */
  public function __toString()
  {
    return htmlspecialchars((string)$this->popsPrimitive, ENT_QUOTES, 'UTF-8');
  }
}
