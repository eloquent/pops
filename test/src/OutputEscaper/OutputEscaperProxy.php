<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OutputEscaper;

use Eloquent\Pops\Proxy;

/**
 * Escapes output for use in HTML.
 */
class OutputEscaperProxy extends Proxy
{
    /**
     * Get the array proxy class.
     *
     * @return string The array proxy class.
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyArray';
    }

    /**
     * Get the class proxy class.
     *
     * @return string The class proxy class.
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyClass';
    }

    /**
     * Get the object proxy class.
     *
     * @return string The object proxy class.
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyObject';
    }

    /**
     * Get the proxy class for primitive values.
     *
     * @return string The proxy class for primitive values.
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyPrimitive';
    }
}
