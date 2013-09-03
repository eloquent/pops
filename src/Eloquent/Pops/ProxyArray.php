<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use Iterator;
use LogicException;

/**
 * A transparent array proxy.
 */
class ProxyArray implements ProxyInterface, ArrayAccess, Countable, Iterator
{
    /**
     * Construct a new array proxy.
     *
     * @param array   $array     The array to wrap.
     * @param boolean|null $recursive True if the array should be recursively proxied.
     */
    public function __construct(array $array, $recursive = null)
    {
        if (null === $recursive) {
            $recursive = false;
        }

        $this->popsArray = $array;
        $this->popsRecursive = $recursive;
        $this->popsInnerIterator = new ArrayIterator($this->popsArray);
    }

    /**
     * Get the wrapped array.
     *
     * @return array The wrapped array.
     */
    public function popsArray()
    {
        return $this->popsArray;
    }

    /**
     * Set the value of an array index.
     *
     * @param integer|string $index The index to set.
     * @param mixed  $value    The new value.
     */
    public function offsetSet($index, $value)
    {
        $this->popsArray[$index] = $value;
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
        return $this->popsProxySubValue($this->popsArray[$index]);
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
        return isset($this->popsArray[$index]);
    }

    /**
     * Remove an array index.
     *
     * @param integer|string $index The index to remove.
     */
    public function offsetUnset($index)
    {
        unset($this->popsArray[$index]);
    }

    /**
     * Get the number of elements in the array.
     *
     * @return integer The number of elements.
     */
    public function count()
    {
        return count($this->popsArray);
    }

    /**
     * Get the current iterator value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return $this->popsProxySubValue(
            $this->popsInnerIterator->current()
        );
    }

    /**
     * Get the current iterator key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        return $this->popsInnerIterator->key();
    }

    /**
     * Move to the next iterator value.
     */
    public function next()
    {
        $this->popsInnerIterator->next();
    }

    /**
     * Rewind to the beginning of the iterator.
     */
    public function rewind()
    {
        $this->popsInnerIterator->rewind();
    }

    /**
     * Returns true if the current iterator position is valid.
     *
     * @return boolean True if the current position is valid.
     */
    public function valid()
    {
        return $this->popsInnerIterator->valid();
    }

    /**
     * Get the string representation of this array.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return strval($this->popsProxySubValue(strval($this->popsArray)));
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
     * Wrap a sub-value in a proxy if recursive proxying is enabled.
     *
     * @param mixed $value The value to wrap.
     *
     * @return mixed The proxied value, or the untouched value.
     */
    protected function popsProxySubValue($value)
    {
        if ($this->popsRecursive) {
            $popsClass = static::popsProxyClass();

            return $popsClass::proxy($value, true);
        }

        return $value;
    }

    private $popsArray;
    private $popsRecursive;
    private $popsInnerIterator;
}
