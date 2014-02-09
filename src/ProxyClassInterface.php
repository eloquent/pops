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
 * The interface implemented by class proxies.
 */
interface ProxyClassInterface extends ProxyInterface
{
    /**
     * Returns true if the wrapped class is recursively proxied.
     *
     * @return boolean True if the wrapped class is recursively proxied.
     */
    public function isPopsRecursive();

    /**
     * Call a static method on the proxied class with support for by-reference
     * arguments.
     *
     * @param string $method     The name of the method to call.
     * @param array  &$arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments);

    /**
     * Call a static method on the proxied class.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, array $arguments);

    /**
     * Set the value of a static property on the proxied class.
     *
     * @param string $property The name of the property to set.
     * @param mixed  $value    The new value.
     */
    public function __set($property, $value);

    /**
     * Get the value of a static property on the proxied class.
     *
     * @param string $property The name of the property to get.
     *
     * @return mixed The value of the property.
     */
    public function __get($property);

    /**
     * Returns true if the supplied static property exists on the proxied class.
     *
     * @param string $property The name of the property to search for.
     *
     * @return boolean True if the property exists.
     */
    public function __isset($property);

    /**
     * Set the value of a static property on the proxied class to null.
     *
     * @param string $property The name of the property to set.
     */
    public function __unset($property);
}
