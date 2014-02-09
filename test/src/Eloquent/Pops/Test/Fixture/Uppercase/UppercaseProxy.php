<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture\Uppercase;

use Eloquent\Pops\Proxy;

class UppercaseProxy extends Proxy
{
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__ . '\UppercaseProxyArray';
    }

    protected static function proxyClassClass()
    {
        return __NAMESPACE__ . '\UppercaseProxyClass';
    }

    protected static function proxyObjectClass()
    {
        return __NAMESPACE__ . '\UppercaseProxyObject';
    }

    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__ . '\UppercaseProxyPrimitive';
    }
}
