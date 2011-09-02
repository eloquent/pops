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

use Countable as CountableInterface;

class Countable extends Object implements CountableInterface
{
  public function count()
  {
    return $this->count;
  }
  
  public $count = 0;
}