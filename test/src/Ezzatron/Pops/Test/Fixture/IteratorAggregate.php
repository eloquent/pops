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

use ArrayIterator;
use IteratorAggregate as IteratorAggregateInterface;

class IteratorAggregate extends Object implements IteratorAggregateInterface
{
  public function __construct(array $values)
  {
    $this->values = $values;
  }

  public function getIterator()
  {
    return new ArrayIterator($this->values);
  }

  public $values;
}