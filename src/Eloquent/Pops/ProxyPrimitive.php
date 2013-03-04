<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

class ProxyPrimitive
{
    /**
     * @param mixed $primitive
     */
    public function __construct($primitive)
    {
        $this->popsPrimitive = $primitive;
    }

    /**
     * @return object
     */
    public function popsPrimitive()
    {
        return $this->popsPrimitive;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->popsPrimitive;
    }

    /**
     * @var mixed
     */
    protected $popsPrimitive;
}
