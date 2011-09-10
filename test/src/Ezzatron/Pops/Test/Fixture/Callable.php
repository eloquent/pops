<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops\Test\Fixture;

class Callable extends Object
{
  public function __construct($returnValue = null)
  {
    $this->returnValue = $returnValue;
  }

  public function __invoke()
  {
    if (null !== $this->returnValue)
    {
      return $this->returnValue;
    }

    return array(__FUNCTION__, func_get_args());
  }

  protected $returnValue;
}