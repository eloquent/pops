<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * The interface implemented by array proxies.
 */
interface ProxyArrayInterface extends
    ProxyInterface,
    ArrayAccess,
    Countable,
    Iterator
{
}
