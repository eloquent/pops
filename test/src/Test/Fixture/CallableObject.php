<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture;

class CallableObject extends Obj
{
    public function __construct($returnValue = null)
    {
        $this->returnValue = $returnValue;
    }

    public function __invoke()
    {
        if (null !== $this->returnValue) {
            return $this->returnValue;
        }

        return [__FUNCTION__, func_get_args()];
    }

    protected $returnValue;
}
