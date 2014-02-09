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

use ArrayIterator;
use Iterator;

/**
 * A transparent array proxy.
 */
class ProxyArray extends AbstractTraversableProxy implements ProxyArrayInterface
{
    /**
     * Get the wrapped array.
     *
     * @deprecated Use popsValue() instead.
     * @see ProxyInterface::popsValue()
     *
     * @return array The wrapped array.
     */
    public function popsArray()
    {
        return $this->popsValue();
    }

    /**
     * Set the value of an array index.
     *
     * @param integer|string $index The index to set.
     * @param mixed          $value The new value.
     */
    public function offsetSet($index, $value)
    {
        $array = $this->popsValue();
        $array[$index] = $value;

        $this->setPopsValue($array);
    }

    /**
     * Get the value of an array index.
     *
     * @param integer|string $index The index to get.
     *
     * @return mixed The value.
     */
    public function offsetGet($index)
    {
        $array = $this->popsValue();

        return $this->popsProxySubValue($array[$index]);
    }

    /**
     * Returns true if the specified array index exists.
     *
     * @param integer|string $index The index to search for.
     *
     * @return boolean True if the index exists.
     */
    public function offsetExists($index)
    {
        $array = $this->popsValue();

        return isset($array[$index]);
    }

    /**
     * Remove an array index.
     *
     * @param integer|string $index The index to remove.
     */
    public function offsetUnset($index)
    {
        $array = $this->popsValue();
        unset($array[$index]);

        $this->setPopsValue($array);
    }

    /**
     * Get the number of elements in the array.
     *
     * @return integer The number of elements.
     */
    public function count()
    {
        return count($this->popsValue());
    }

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return strval($this->popsProxySubValue(strval($this->popsValue())));
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
        if (!is_array($value)) {
            throw new Exception\InvalidTypeException($value, 'array');
        }
    }

    /**
     * Create an iterator for the wrapped object.
     *
     * @return Iterator An iterator for the wrapped object.
     */
    protected function popsCreateInnerIterator()
    {
        return new ArrayIterator($this->popsValue());
    }
}
