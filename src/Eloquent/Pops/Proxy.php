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

use Eloquent\Pops\Safe\SafeInterface;
use LogicException;
use ReflectionClass;

/**
 * A transparent proxy.
 */
class Proxy
{
    /**
     * Wrap the supplied value in a proxy.
     *
     * @param mixed   $value     The value to wrap.
     * @param boolean|null $recursive True if the value should be recursively proxied.
     *
     * @return ProxyInterface The proxied value.
     */
    public static function proxy($value, $recursive = null)
    {
        if ($value instanceof SafeInterface) {
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
     * Wrap the supplied array in a proxy.
     *
     * @param array   $array     The array to wrap.
     * @param boolean|null $recursive True if the array should be recursively proxied.
     *
     * @return ProxyArray The proxied array.
     */
    public static function proxyArray($array, $recursive = null)
    {
        $class = new ReflectionClass(static::proxyArrayClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * Wrap the supplied class in a non-static proxy.
     *
     * @param string  $class     The name of the class to wrap.
     * @param boolean|null $recursive True if the class should be recursively proxied.
     *
     * @return ProxyClass The non-static class proxy.
     */
    public static function proxyClass($class, $recursive = null)
    {
        $proxyClassClass = new ReflectionClass(static::proxyClassClass());

        return $proxyClassClass->newInstanceArgs(func_get_args());
    }

    /**
     * Wrap the supplied class in a static proxy.
     *
     * @param string  $class     The name of the class to wrap.
     * @param boolean|null $recursive True if the class should be recursively proxied.
     * @param string  $proxyClass
     *
     * @return string The static class proxy.
     */
    public static function proxyClassStatic(
        $class,
        $recursive = null,
        $proxyClass = null
    ) {
        $method = static::proxyClassClass() . '::popsGenerateStaticClassProxy';

        return call_user_func_array($method, func_get_args());
    }

    /**
     * Wrap the supplied object in a proxy.
     *
     * @param object  $object    The object to wrap.
     * @param boolean|null $recursive True if the object should be recursively proxied.
     *
     * @return ProxyObject The proxied object.
     */
    public static function proxyObject($object, $recursive = null)
    {
        $class = new ReflectionClass(static::proxyObjectClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * Wrap the supplied primitive value in a proxy.
     *
     * @param mixed $primitive The primitive value to wrap.
     *
     * @return ProxyPrimitive The proxied value.
     */
    public static function proxyPrimitive($primitive)
    {
        $class = new ReflectionClass(static::proxyPrimitiveClass());

        return $class->newInstanceArgs(func_get_args());
    }

    /**
     * Get the array proxy class.
     *
     * @return string The array proxy class.
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__ . '\ProxyArray';
    }

    /**
     * Get the class proxy class.
     *
     * @return string The class proxy class.
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__ . '\ProxyClass';
    }

    /**
     * Get the object proxy class.
     *
     * @return string The object proxy class.
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__ . '\ProxyObject';
    }

    /**
     * Get the proxy class for primitive values.
     *
     * @return string The proxy class for primitive values.
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__ . '\ProxyPrimitive';
    }
}
