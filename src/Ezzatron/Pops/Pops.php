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

use Ezzatron\Pops\Safe\Proxy as SafeProxy;
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
    if ($value instanceof SafeProxy)
    {
      return $value;
    }
    if (is_object($value))
    {
      return static::proxyObject($value, $recursive);
    }
    if (is_array($value))
    {
      return static::proxyArray($value, $recursive);
    }

    return static::proxyPrimitive($value);
  }

  /**
   * @param array $array
   * @param boolean $recursive
   *
   * @return ProxyArray
   */
  static public function proxyArray($array, $recursive = null)
  {
    $class = new ReflectionClass(static::proxyArrayClass());

    return $class->newInstanceArgs(func_get_args());
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
   * @return string
   */
  static public function proxyClassStatic($class, $recursive = null, $proxyClass = null)
  {
    $method = static::proxyClassClass().'::_popsGenerateStaticClassProxy';

    return call_user_func_array($method, func_get_args());
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
  static protected function proxyArrayClass()
  {
    return static::proxyDynamicClassSelect('ProxyArray');
  }

  /**
   * @return string
   */
  static protected function proxyClassClass()
  {
    return static::proxyDynamicClassSelect('ProxyClass');
  }

  /**
   * @return string
   */
  static protected function proxyObjectClass()
  {
    return static::proxyDynamicClassSelect('ProxyObject');
  }

  /**
   * @return string
   */
  static protected function proxyPrimitiveClass()
  {
    return static::proxyDynamicClassSelect('ProxyPrimitive');
  }

  /**
   * @param string $proxyClass
   *
   * @return string
   */
  static protected function proxyDynamicClassSelect($proxyClass)
  {
    $class = new ReflectionClass(get_called_class());
    $namespace = $class->getNamespaceName();
    $namespaceProxyClass = $namespace.'\\'.$proxyClass;

    if (class_exists($namespaceProxyClass))
    {
      return $namespaceProxyClass;
    }

    $parent = $class->getParentClass()->getName();
    $method = lcfirst($proxyClass).'Class';

    return $parent::$method();
  }
}