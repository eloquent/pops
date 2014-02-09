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

use Eloquent\Pops\ProxyPrimitive;

/**
 * A proxy for primitives that prevents recusive proxying.
 */
class SafeProxyPrimitive extends ProxyPrimitive implements SafeInterface
{
}
