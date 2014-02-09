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

/**
 * An abstract base class for implementing traversable proxies.
 */
abstract class AbstractTraversableProxy extends AbstractProxy implements
    TraversableProxyInterface
{
    /**
     * Construct a new traversable proxy.
     *
     * @param mixed        $value       The value to wrap.
     * @param boolean|null $isRecursive True if the wrapped value should be recursively proxied.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    public function __construct($value, $isRecursive = null)
    {
        if (null === $isRecursive) {
            $isRecursive = false;
        }

        parent::__construct($value);

        $this->isPopsRecursive = $isRecursive;
    }

    /**
     * Returns true if the wrapped value is recursively proxied.
     *
     * @return boolean True if the wrapped value is recursively proxied.
     */
    public function isPopsRecursive()
    {
        return $this->isPopsRecursive;
    }

    /**
     * Get the current iterator value.
     *
     * @return mixed The current value.
     */
    public function current()
    {
        return $this->popsProxySubValue($this->popsInnerIterator()->current());
    }

    /**
     * Get the current iterator key.
     *
     * @return mixed The current key.
     */
    public function key()
    {
        return $this->popsInnerIterator()->key();
    }

    /**
     * Move to the next iterator value.
     */
    public function next()
    {
        $this->popsInnerIterator()->next();
    }

    /**
     * Rewind to the beginning of the iterator.
     */
    public function rewind()
    {
        $this->popsInnerIterator()->rewind();
    }

    /**
     * Returns true if the current iterator position is valid.
     *
     * @return boolean True if the current position is valid.
     */
    public function valid()
    {
        return $this->popsInnerIterator()->valid();
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
        if ($this->isPopsRecursive()) {
            $popsClass = static::popsProxyClass();

            return $popsClass::proxy($value, true);
        }

        return $value;
    }

    /**
     * Get an iterator for the wrapped object.
     *
     * @return Iterator An iterator for the wrapped object.
     */
    protected function popsInnerIterator()
    {
        if (null === $this->popsInnerIterator) {
            $this->popsInnerIterator = $this->popsCreateInnerIterator();
        }

        return $this->popsInnerIterator;
    }

    /**
     * Create an iterator for the wrapped object.
     *
     * @return Iterator An iterator for the wrapped object.
     */
    abstract protected function popsCreateInnerIterator();

    private $isPopsRecursive;
    private $popsInnerIterator;
}
