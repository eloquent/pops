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

use ArrayAccess;
use Countable;
use Iterator;

/**
 * The interface implemented by object proxies.
 */
interface ProxyObjectInterface extends
    ProxyInterface,
    ArrayAccess,
    Countable,
    Iterator
{
    /**
     * Call a method on the wrapped object with support for by-reference
     * arguments.
     *
     * @param string $method     The name of the method to call.
     * @param array  &$arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments);

    /**
     * Call a method on the wrapped object.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, array $arguments);

    /**
     * Invoke this object.
     *
     * @return mixed The result of invocation.
     */
    public function __invoke();

    /**
     * Set the value of a property on the wrapped object.
     *
     * @param string $property The property name.
     * @param mixed  $value    The new value.
     */
    public function __set($property, $value);

    /**
     * Get the value of a property from the wrapped object.
     *
     * @param string $property The property name.
     *
     * @return mixed The property value.
     */
    public function __get($property);

    /**
     * Returns true if the property exists on the wrapped object.
     *
     * @param string $property The name of the property to search for.
     *
     * @return boolean True if the property exists.
     */
    public function __isset($property);

    /**
     * Unset a property from the wrapped object.
     *
     * @param string $property The property name.
     */
    public function __unset($property);
}
