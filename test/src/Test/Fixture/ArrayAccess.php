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

use ArrayAccess as ArrayAccessInterface;

class ArrayAccess extends Obj implements ArrayAccessInterface
{
    public function offsetSet($property, $value): void
    {
        $this->values[$property] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($property)
    {
        return $this->values[$property];
    }

    public function offsetExists($property): bool
    {
        return array_key_exists($property, $this->values);
    }

    public function offsetUnset($property): void
    {
        unset($this->values[$property]);
    }

    public $values = [];
}
