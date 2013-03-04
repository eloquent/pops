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

use Eloquent\Pops\Safe\Safe;
use ReflectionClass;

class Pops
{
    /**
     * @param mixed $value
     * @param boolean $recursive
     *
     * @return Proxy
     */
    public static function proxy($value, $recursive = null)
    {
        if ($value instanceof Safe) {
            return $value;
        }
        if (is_object($value)) {
            return static::proxyObject($value, $recursive);
        }
        if (is_array($value)) {
            return static::proxyArray($value, $recursive);
        }

        return static::proxyPrimitive($value);
    }

    /**
     * @param array $array
     * @param boolean $recursive
     *
     * @return ProxyArray
     */
    public static function proxyArray($array, $recursive = null)
    {
        $class = new ReflectionClass(static::proxyArrayClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * @param string $class
     * @param boolean $recursive
     *
     * @return ProxyClass
     */
    public static function proxyClass($class, $recursive = null)
    {
        $proxyClassClass = new ReflectionClass(static::proxyClassClass());

        return $proxyClassClass->newInstanceArgs(func_get_args());
    }

    /**
     * @param string $class
     * @param boolean $recursive
     * @param string $proxyClass
     *
     * @return string
     */
    public static function proxyClassStatic(
        $class,
        $recursive = null,
        $proxyClass = null
    ) {
        $method = static::proxyClassClass().'::popsGenerateStaticClassProxy';

        return call_user_func_array($method, func_get_args());
    }

    /**
     * @param object $object
     * @param boolean $recursive
     *
     * @return ProxyObject
     */
    public static function proxyObject($object, $recursive = null)
    {
        $class = new ReflectionClass(static::proxyObjectClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * @param mixed $primitive
     *
     * @return ProxyPrimitive
     */
    public static function proxyPrimitive($primitive)
    {
        $class = new ReflectionClass(static::proxyPrimitiveClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * @return string
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__.'\ProxyArray';
    }

    /**
     * @return string
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__.'\ProxyClass';
    }

    /**
     * @return string
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__.'\ProxyObject';
    }

    /**
     * @return string
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__.'\ProxyPrimitive';
    }
}
