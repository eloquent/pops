<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Safe;

use Eloquent\Pops\Pops;

class SafeProxy extends Pops
{
    /**
     * @return string
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__.'\SafeProxyArray';
    }

    /**
     * @return string
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__.'\SafeProxyClass';
    }

    /**
     * @return string
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__.'\SafeProxyObject';
    }

    /**
     * @return string
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__.'\SafeProxyPrimitive';
    }
}
