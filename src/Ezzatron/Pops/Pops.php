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

use ReflectionClass;

class Pops
{
  /**
   * @param mixed $value
   * @param boolean $recursive
   *
   * @return Proxy
   */
  static public function proxy($value, $recursive = null)
  {
    if (is_object($value))
    {
      return static::proxyObject($value, $recursive);
    }

    return static::proxyPrimitive($value);
  }

  /**
   * @param string $class
   * @param boolean $recursive
   *
   * @return ProxyClass
   */
  static public function proxyClass($class, $recursive = null)
  {
    $proxyClassClass = new ReflectionClass(static::proxyClassClass());

    return $proxyClassClass->newInstanceArgs(func_get_args());
  }

  /**
   * @param string $class
   * @param boolean $recursive
   * @param string $proxyClass
   *
   * @return $string
   */
  static public function proxyClassStatic($class, $recursive = null, $proxyClass = null)
  {
    $proxyClassClass = static::proxyClassClass();

    return $proxyClassClass::_popsGenerateStaticClassProxy($class, $recursive, $proxyClass);
  }

  /**
   * @param object $object
   * @param boolean $recursive
   *
   * @return ProxyObject
   */
  static public function proxyObject($object, $recursive = null)
  {
    $class = new ReflectionClass(static::proxyObjectClass());

    return $class->newInstanceArgs(func_get_args());
  }

  /**
   * @param mixed $primitive
   *
   * @return ProxyPrimitive
   */
  static public function proxyPrimitive($primitive)
  {
    $class = new ReflectionClass(static::proxyPrimitiveClass());

    return $class->newInstanceArgs(func_get_args());
  }

  /**
   * @return string
   */
  static protected function proxyClassClass()
  {
    return __NAMESPACE__.'\ProxyClass';
  }

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
  static protected function proxyPrimitiveClass()
  {
    return __NAMESPACE__.'\ProxyPrimitive';
  }
}