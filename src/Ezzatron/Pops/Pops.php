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

    return $value;
  }

  /**
   * @param string $class
   * @param boolean $recursive
   *
   * @return ProxyClass
   */
  static public function proxyClass($class, $recursive = null)
  {
    $class = new ReflectionClass(static::proxyClassClass());

    return $class->newInstanceArgs(func_get_args());
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
    $classDef = static::proxyClassStaticDefinition($class, $recursive, $proxyClass);
    eval($classDef);

    return $proxyClass;
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

  /**
   * @param string $originalClass
   * @param boolean $recursive
   * @param string $proxyClass
   *
   * @return string
   */
  static protected function proxyClassStaticDefinition($originalClass, $recursive, &$proxyClass)
  {
    $proxyClass = static::proxyClassStaticProxyClass($originalClass, $proxyClass);

    return
      static::proxyClassStaticDefinitionHeader($proxyClass)
      .' { '
      .static::proxyClassStaticDefinitionBody($originalClass, $recursive)
      .' }'
    ;
  }

  /**
   * @param string $originalClass
   * @param string $proxyClass
   *
   * @return string
   */
  static protected function proxyClassStaticProxyClass($originalClass, $proxyClass)
  {
    if (null === $proxyClass)
    {
      $originalClassParts = explode('\\', $originalClass);
      $proxyClassPrefix = array_pop($originalClassParts).'_Pops_';
      $proxyClass = uniqid($proxyClassPrefix);
    }

    return $proxyClass;
  }

  /**
   * @param string $proxyClass
   *
   * @return string
   */
  static protected function proxyClassStaticDefinitionHeader($proxyClass)
  {
    return 'class '.$proxyClass.' extends '.static::proxyClassClass();
  }

  /**
   * @param string $originalClass
   * @param boolean $recursive
   *
   * @return string
   */
  static protected function proxyClassStaticDefinitionBody($originalClass, $recursive)
  {
    return
      'static protected $_popsStaticOriginalClass = '.var_export($originalClass, true).';'
      .'static protected $_popsStaticRecursive = '.var_export($recursive, true).';'
    ;
  }
}