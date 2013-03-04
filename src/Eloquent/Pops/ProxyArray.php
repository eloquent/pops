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
use ReflectionClass;

class ProxyArray implements Proxy, ArrayAccess, Countable, Iterator
{
    /**
     * @param array $array
     * @param boolean $recursive
     */
    public function __construct(array $array, $recursive = null)
    {
        if (null === $recursive) {
            $recursive = false;
        }
        if (!is_bool($recursive)) {
            throw new InvalidArgumentException(
                'Provided value is not a boolean'
            );
        }

        $this->popsArray = $array;
        $this->popsRecursive = $recursive;
        $this->popsInnerIterator = new ArrayIterator($this->popsArray);
    }

    /**
     * @return array
     */
    public function popsArray()
    {
        return $this->popsArray;
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function offsetSet($property, $value)
    {
        $this->popsArray[$property] = $value;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function offsetGet($property)
    {
        return $this->popsProxySubValue(
            $this->popsArray[$property]
        );
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function offsetExists($property)
    {
        return isset($this->popsArray[$property]);
    }

    /**
     * @param string $property
     */
    public function offsetUnset($property)
    {
        unset($this->popsArray[$property]);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->popsArray);
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->popsProxySubValue(
            $this->popsInnerIterator->current()
        );
    }

    /**
     * @return scalar
     */
    public function key()
    {
        return $this->popsInnerIterator->key();
    }

    public function next()
    {
        $this->popsInnerIterator->next();
    }

    public function rewind()
    {
        $this->popsInnerIterator->rewind();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->popsInnerIterator->valid();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->popsProxySubValue(
            (string) $this->popsArray
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
     * @var array
     */
    protected $popsArray;

    /**
     * @var boolean
     */
    protected $popsRecursive;

    /**
     * @var Iterator
     */
    protected $popsInnerIterator;
}
