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
 * An abstract base class for implementing proxies.
 */
abstract class AbstractProxy implements ProxyInterface
{
    /**
     * Construct a new proxy.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    public function __construct($value)
    {
        $this->setPopsValue($value);
    }

    /**
     * Set the wrapped value.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    public function setPopsValue($value)
    {
        $this->assertPopsValue($value);

        $this->value = $value;
    }

    /**
     * Get the wrapped value.
     *
     * @return mixed The wrapped value.
     */
    public function popsValue()
    {
        return $this->value;
    }

    /**
     * Throw an exception if the supplied value is an incorrect type for this
     * proxy.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    abstract protected function assertPopsValue($value);

    private $value;
}
