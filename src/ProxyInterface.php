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
 * The interface implemented by proxied values.
 */
interface ProxyInterface
{
    /**
     * Set the wrapped value.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    public function setPopsValue($value);

    /**
     * Get the wrapped value.
     *
     * @return mixed The wrapped value.
     */
    public function popsValue();

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString();
}
