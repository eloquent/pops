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
   *
   * @return Proxy
   */
  static public function proxy($value)
  {
    return static::proxyObject($value);
  }

  /**
   * @param string $class
   *
   * @return ProxyClass
   */
  static public function proxyClass($class)
  {
    $class = new ReflectionClass(static::proxyClassClass());

    return $class->newInstanceArgs(func_get_args());
  }

  /**
   * @param string $class
   * @param string $proxyClass
   *
   * @return $string
   */
  static public function proxyClassStatic($class, $proxyClass = null)
  {
    $classDef = static::proxyClassStaticDefinition($class, $proxyClass);
    eval($classDef);

    return $proxyClass;
  }

  /**
   * @param object $object
   *
   * @return ProxyObject
   */
  static public function proxyObject($object)
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
   * @param string $proxyClass
   *
   * @return string
   */
  static protected function proxyClassStaticDefinition($originalClass, &$proxyClass)
  {
    $proxyClass = static::proxyClassStaticProxyClass($originalClass, $proxyClass);

    return
      static::proxyClassStaticDefinitionHeader($proxyClass)
      .' { '
      .static::proxyClassStaticDefinitionBody($originalClass)
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
   *
   * @return string
   */
  static protected function proxyClassStaticDefinitionBody($originalClass)
  {
    return 'static protected $_popsStaticOriginalClass = '.var_export($originalClass, true).';';
  }
}