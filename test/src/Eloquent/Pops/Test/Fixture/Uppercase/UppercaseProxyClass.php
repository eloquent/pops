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

use Eloquent\Pops\ProxyClass;

class UppercaseProxyClass extends ProxyClass
{
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\UppercaseProxy';
    }
}
