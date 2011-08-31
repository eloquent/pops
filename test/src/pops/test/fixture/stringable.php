<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops\Test\Fixture;

class Stringable extends Object
{
  public function __toString()
  {
    return $this->string;
  }

  public $string = '';
}