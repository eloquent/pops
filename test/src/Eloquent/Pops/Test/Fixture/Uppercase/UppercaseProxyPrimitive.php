<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture\Uppercase;

use Eloquent\Pops\ProxyPrimitive;

class UppercaseProxyPrimitive extends ProxyPrimitive
{
    /**
     * @return string
     */
    public function __toString()
    {
        return mb_strtoupper((string) $this->popsPrimitive);
    }
}
