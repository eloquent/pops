<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Eloquent\Pops\ProxyObject;

class UppercaseProxy extends ProxyObject
{
  public function __call($method, array $arguments)
  {
    return strtoupper(parent::__call($method, $arguments));
  }

  public function __get($property)
  {
    return strtoupper(parent::__get($property));
  }
}