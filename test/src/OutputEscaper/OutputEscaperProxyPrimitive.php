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

use Eloquent\Pops\ProxyPrimitive;

/**
 * Wraps a primitive to escape its value for use in HTML.
 */
class OutputEscaperProxyPrimitive extends ProxyPrimitive
{
    /**
     * Get the HTML-escaped version of this primitive.
     *
     * @return string The HTML-secaped version of this primitive.
     */
    public function __toString()
    {
        return htmlspecialchars(
            strval($this->popsPrimitive()),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
