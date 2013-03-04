<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture\Uppercase;

use Eloquent\Pops\Pops;

class UppercaseProxy extends Pops
{
    /**
     * @return string
     */
    static protected function proxyArrayClass()
    {
        return __NAMESPACE__.'\UppercaseProxyArray';
    }

    /**
     * @return string
     */
    static protected function proxyClassClass()
    {
        return __NAMESPACE__.'\UppercaseProxyClass';
    }

    /**
     * @return string
     */
    static protected function proxyObjectClass()
    {
        return __NAMESPACE__.'\UppercaseProxyObject';
    }

    /**
     * @return string
     */
    static protected function proxyPrimitiveClass()
    {
        return __NAMESPACE__.'\UppercaseProxyPrimitive';
    }
}
