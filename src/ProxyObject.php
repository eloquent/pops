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

use Iterator;
use IteratorAggregate;
use LogicException;

/**
 * A transparent object proxy.
 */
class ProxyObject extends AbstractTraversableProxy implements
    ProxyObjectInterface
{
    /**
     * Get the wrapped object.
     *
     * @deprecated Use popsValue() instead.
     * @see ProxyInterface::popsValue()
     *
     * @return object The wrapped object.
     */
    public function popsObject()
    {
        return $this->popsValue();
    }

    /**
     * Call a method on the wrapped object with support for by-reference
     * arguments.
     *
     * @param string $method     The name of the method to call.
     * @param array  &$arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments)
    {
        return $this->popsProxySubValue(
            call_user_func_array(array($this->popsValue(), $method), $arguments)
        );
    }

    /**
     * Call a method on the wrapped object.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, array $arguments)
    {
        return $this->popsCall($method, $arguments);
    }

    /**
     * Invoke this object.
     *
     * @return mixed The result of invocation.
     */
    public function __invoke()
    {
        return $this->__call('__invoke', func_get_args());
    }

    /**
     * Set the value of a property on the wrapped object.
     *
     * @param string $property The property name.
     * @param mixed  $value    The new value.
     */
    public function __set($property, $value)
    {
        $this->popsValue()->$property = $value;
    }

    /**
     * Get the value of a property from the wrapped object.
     *
     * @param string $property The property name.
     *
     * @return mixed The property value.
     */
    public function __get($property)
    {
        return $this->popsProxySubValue($this->popsValue()->$property);
    }

    /**
     * Returns true if the property exists on the wrapped object.
     *
     * @param string $property The name of the property to search for.
     *
     * @return boolean True if the property exists.
     */
    public function __isset($property)
    {
        return isset($this->popsValue()->$property);
    }

    /**
     * Unset a property from the wrapped object.
     *
     * @param string $property The property name.
     */
    public function __unset($property)
    {
        unset($this->popsValue()->$property);
    }

    /**
     * Set a value on the wrapped object using the array access interface.
     *
     * @param string $key   The key to set.
     * @param mixed  $value The new value.
     */
    public function offsetSet($key, $value)
    {
        $this->__call('offsetSet', func_get_args());
    }

    /**
     * Get a value from the wrapped object using the array access interface.
     *
     * @param string $key The key to get.
     *
     * @return mixed The value.
     */
    public function offsetGet($key)
    {
        return $this->__call('offsetGet', func_get_args());
    }

    /**
     * Returns true if the supplied key exists on the wrapped object according
     * to the array access interface.
     *
     * @param string $key The key to search for.
     *
     * @return boolean True if the key exists.
     */
    public function offsetExists($key)
    {
        return $this->__call('offsetExists', func_get_args());
    }

    /**
     * Remove a key from the wrapped object using the array access interface.
     *
     * @param string $key The key to remove.
     */
    public function offsetUnset($key)
    {
        $this->__call('offsetUnset', func_get_args());
    }

    /**
     * Return the result of the wrapped object's count method.
     *
     * @return integer The wrapped object's count.
     */
    public function count()
    {
        return $this->__call('count', array());
    }

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return strval($this->__call('__toString', array()));
    }

    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\Proxy';
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
        if (!is_object($value)) {
            throw new Exception\InvalidTypeException($value, 'object');
        }
    }

    /**
     * Create an iterator for the wrapped object.
     *
     * @return Iterator An iterator for the wrapped object.
     */
    protected function popsCreateInnerIterator()
    {
        if ($this->popsValue() instanceof Iterator) {
            $iterator = $this->popsValue();
        } elseif ($this->popsValue() instanceof IteratorAggregate) {
            $iterator = $this->popsValue()->getIterator();
        } else {
            throw new LogicException(
                'Proxied object is not an instance of Traversable'
            );
        }

        return $iterator;
    }
}
