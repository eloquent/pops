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

/**
 * The interface implemented by proxied traversable values.
 */
interface TraversableProxyInterface extends ProxyInterface
{
    /**
     * Returns true if the wrapped value is recursively proxied.
     *
     * @return boolean True if the wrapped value is recursively proxied.
     */
    public function isPopsRecursive();
}
