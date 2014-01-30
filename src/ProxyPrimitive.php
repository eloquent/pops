<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

/**
 * A transparent primitive value proxy.
 */
class ProxyPrimitive
{
    /**
     * Construct a new primitive value proxy.
     *
     * @param scalar|null $primitive The primitive value to wrap.
     */
    public function __construct($primitive)
    {
        $this->popsPrimitive = $primitive;
    }

    /**
     * Get the wrapped value.
     *
     * @return scalar|null The wrapped value.
     */
    public function popsPrimitive()
    {
        return $this->popsPrimitive;
    }

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return strval($this->popsPrimitive);
    }

    private $popsPrimitive;
}
