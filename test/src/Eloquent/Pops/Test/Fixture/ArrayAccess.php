<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture;

use ArrayAccess as ArrayAccessInterface;

class ArrayAccess extends Object implements ArrayAccessInterface
{
    public function offsetSet($property, $value)
    {
        $this->values[$property] = $value;
    }

    public function offsetGet($property)
    {
        return $this->values[$property];
    }

    public function offsetExists($property)
    {
        return array_key_exists($property, $this->values);
    }

    public function offsetUnset($property)
    {
        unset($this->values[$property]);
    }

    public $values = array();
}
