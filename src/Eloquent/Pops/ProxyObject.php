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
use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
use LogicException;

/**
 * A transparent object proxy.
 */
class ProxyObject implements ProxyInterface, ArrayAccess, Countable, Iterator
{
    /**
     * Construct a new object proxy.
     *
     * @param object  $object The object to wrap.
     * @param boolean|null $recursive True if the object should be recursively proxied.
     */
    public function __construct($object, $recursive = null)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException(
                'Provided value is not an object'
            );
        }

        if (null === $recursive) {
            $recursive = false;
        }
        if (!is_bool($recursive)) {
            throw new InvalidArgumentException(
                'Provided value is not a boolean'
            );
        }

        $this->popsObject = $object;
        $this->popsRecursive = $recursive;
    }

    /**
     * Get the wrapped object.
     *
     * @return object The wrapped object.
     */
    public function popsObject()
    {
        return $this->popsObject;
    }

    /**
     * Call a method on the wrapped object with support for by-reference
     * arguments.
     *
     * @param string $method    The name of the method to call.
     * @param array  &$arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments)
    {
        return $this->popsProxySubValue(
            call_user_func_array(array($this->popsObject, $method), $arguments)
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
     * Set the value of a property on the wrapped object.
     *
     * @param string $property The property name.
     * @param mixed  $value The new value.
     */
    public function __set($property, $value)
    {
        $this->popsObject->$property = $value;
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
        return $this->popsProxySubValue(
            $this->popsObject->$property
        );
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
        return isset($this->popsObject->$property);
    }

    /**
     * Unset a property from the wrapped object.
     *
     * @param string $property The property name.
     */
    public function __unset($property)
    {
        unset($this->popsObject->$property);
    }

    /**
     * Set a value on the wrapped object using the ArrayAccess interface.
     *
     * @param string $key The key to set.
     * @param mixed  $value The new value.
     */
    public function offsetSet($key, $value)
    {
        $this->__call('offsetSet', func_get_args());
    }

    /**
     * Get a value from the wrapped object using the ArrayAccess interface.
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
     * to the ArrayAccess interface.
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
     * Remove a key from the wrapped object using the ArrayAccess interface.
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
     * @return string
     */
    public function __toString()
    {
        return strval($this->__call('__toString', array()));
    }

    /**
     * Get the string representation of this object.
     *
     * @return string The string representation.
     */
    public function __invoke()
    {
        if (!method_exists($this->popsObject, '__invoke')) {
            throw new BadMethodCallException(
                sprintf(
                    'Call to undefined method %s::__invoke()',
                    get_class($this->popsObject)
                )
            );
        }

        return $this->popsProxySubValue(
            call_user_func_array($this->popsObject, func_get_args())
        );
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
     * Get an iterator for the wrapped object.
     *
     * @return Iterator An iterator for the wrapped object.
     */
    protected function popsInnerIterator()
    {
        if (null !== $this->popsInnerIterator) {
            return $this->popsInnerIterator;
        }

        if ($this->popsObject instanceof Iterator) {
            $this->popsInnerIterator = $this->popsObject;
        } elseif ($this->popsObject instanceof IteratorAggregate) {
            $this->popsInnerIterator = $this->popsObject->getIterator();
        } else {
            throw new LogicException(
                'Proxied object is not an instance of Traversable'
            );
        }

        return $this->popsInnerIterator;
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

    private $popsObject;
    private $popsRecursive;
    private $popsInnerIterator;
}
