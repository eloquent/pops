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
use ReflectionClass;

class ProxyObject implements Proxy, ArrayAccess, Countable, Iterator
{
    /**
     * @param object $object
     * @param boolean $recursive
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
     * @return object
     */
    public function popsObject()
    {
        return $this->popsObject;
    }

    /**
     * @param string $method
     * @param array &$arguments
     *
     * @return mixed
     */
    public function popsCall($method, array &$arguments)
    {
        return $this->popsProxySubValue(
            call_user_func_array(array($this->popsObject, $method), $arguments)
        );
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        return $this->popsCall($method, $arguments);
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $this->popsObject->$property = $value;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        return $this->popsProxySubValue(
            $this->popsObject->$property
        );
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        return isset($this->popsObject->$property);
    }

    /**
     * @param string $property
     */
    public function __unset($property)
    {
        unset($this->popsObject->$property);
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function offsetSet($property, $value)
    {
        $this->__call('offsetSet', func_get_args());
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function offsetGet($property)
    {
        return $this->__call('offsetGet', func_get_args());
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function offsetExists($property)
    {
        return $this->__call('offsetExists', func_get_args());
    }

    /**
     * @param string $property
     */
    public function offsetUnset($property)
    {
        $this->__call('offsetUnset', func_get_args());
    }

    /**
     * @return integer
     */
    public function count()
    {
        return $this->__call('count', array());
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->popsProxySubValue(
            $this->popsInnerIterator()->current()
        );
    }

    /**
     * @return scalar
     */
    public function key()
    {
        return $this->popsInnerIterator()->key();
    }

    public function next()
    {
        $this->popsInnerIterator()->next();
    }

    public function rewind()
    {
        $this->popsInnerIterator()->rewind();
    }

    /**
     * @return boolean
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
        return (string) $this->__call('__toString', array());
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        if (!method_exists($this->popsObject, '__invoke')) {
            throw new BadMethodCallException(
                'Call to undefined method '.
                get_class($this->popsObject).'::__invoke()'
            );
        }

        return $this->popsProxySubValue(
            call_user_func_array($this->popsObject, func_get_args())
        );
    }

    /**
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\Pops';
    }

    /**
     * @return Iterator
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
     * @param mixed $value
     *
     * @return mixed
     */
    protected function popsProxySubValue($value)
    {
        if ($this->popsRecursive) {
            $popsClass = static::popsProxyClass();

            return $popsClass::proxy($value, true);
        }

        return $value;
    }

    /**
     * @var object
     */
    protected $popsObject;

    /**
     * @var boolean
     */
    protected $popsRecursive;

    /**
     * @var Iterator
     */
    protected $popsInnerIterator;
}
