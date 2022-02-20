<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture;

use ArrayIterator;
use IteratorAggregate as IteratorAggregateInterface;

class IteratorAggregate extends Obj implements IteratorAggregateInterface
{
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    public $values;
}
