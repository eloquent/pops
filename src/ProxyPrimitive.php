<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops;

/**
 * A transparent primitive value proxy.
 */
class ProxyPrimitive extends AbstractProxy implements ProxyPrimitiveInterface
{
    /**
     * Get the wrapped value.
     *
     * @deprecated Use popsValue() instead.
     * @see ProxyInterface::popsValue()
     *
     * @return scalar|null The wrapped value.
     */
    public function popsPrimitive()
    {
        return $this->popsValue();
    }

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return strval($this->popsValue());
    }

    /**
     * Throw an exception if the supplied value is an incorrect type for this
     * proxy.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    protected function assertPopsValue($value)
    {
        if (!is_scalar($value)) {
            throw new Exception\InvalidTypeException($value, 'scalar|null');
        }
    }
}
