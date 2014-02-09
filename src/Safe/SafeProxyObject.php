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

use Eloquent\Pops\ProxyObject;

/**
 * An object proxy that prevents recusive proxying.
 */
class SafeProxyObject extends ProxyObject implements SafeInterface
{
    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\SafeProxy';
    }
}
