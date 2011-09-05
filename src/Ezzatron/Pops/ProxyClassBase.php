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

use LogicException;

abstract class ProxyClassBase
{
  /**
   * @param string $method
   * @param array $arguments
   *
   * @return mixed
   */
  static public function __callStatic($method, array $arguments)
  {
    return call_user_func_array(array(self::_popsProxy(), $method), $arguments);
  }

  /**
   * @return ProxyClass
   */
  static public function _popsProxy()
  {
    $proxyClassClass = static::$_popsProxyClassClass;
    $class = static::$_popsClass;
    if (null === $class)
    {
      throw new LogicException('This class should not be called directly.');
    }

    if (!isset(self::$_popsProxies[$proxyClassClass][$class]))
    {
      self::$_popsProxies[$proxyClassClass][$class] = new $proxyClassClass($class);
    }

    return self::$_popsProxies[$proxyClassClass][$class];
  }

  /**
   * @var array
   */
  static protected $_popsProxies = array();

  /**
   * @var string
   */
  static protected $_popsProxyClassClass = 'Ezzatron\Pops\ProxyClass';

  /**
   * @var string
   */
  static protected $_popsClass;
}