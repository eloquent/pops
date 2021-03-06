<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OutputEscaper;

use Eloquent\Pops\ProxyObject;

/**
 * Wraps an object to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyObject extends ProxyObject
{
    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxy';
    }
}
