<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops\Test;

use PHPUnit_Framework_TestCase;
use Ezzatron\Pops\Proxy;
use Ezzatron\Pops\ProxyClass;

class TestCase extends PHPUnit_Framework_TestCase
{
  /**
   * @param Proxy $proxy
   * @param string $method
   * @param array $arguments
   * @param boolean $magic
   */
  protected function assertPopsProxyCall(Proxy $proxy, $method, array $arguments = null, $magic = null)
  {
    $actual = call_user_func_array(array($proxy, $method), $arguments);
    
    if ($magic)
    {
      $arguments = array($method, $arguments);
      
      if ($proxy instanceof ProxyClass)
      {
        $method = '__callStatic';
      }
      else
      {
        $method = '__call';
      }
    }
    
    $expected = array($method, $arguments);

    $this->assertEquals($expected, $actual);
  }
}