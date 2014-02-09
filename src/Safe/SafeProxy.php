<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Safe;

use Eloquent\Pops\Proxy;

/**
 * A proxy for wrapping values to prevent recursive proxying.
 */
class SafeProxy extends Proxy
{
    /**
     * Get the array proxy class.
     *
     * @return string The array proxy class.
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__ . '\SafeProxyArray';
    }

    /**
     * Get the class proxy class.
     *
     * @return string The class proxy class.
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__ . '\SafeProxyClass';
    }

    /**
     * Get the object proxy class.
     *
     * @return string The object proxy class.
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__ . '\SafeProxyObject';
    }

    /**
     * Get the proxy class for primitive values.
     *
     * @return string The proxy class for primitive values.
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__ . '\SafeProxyPrimitive';
    }
}
