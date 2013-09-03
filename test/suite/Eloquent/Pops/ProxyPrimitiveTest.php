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

use Eloquent\Pops\Test\TestCase;

class ProxyPrimitiveTest extends TestCase
{
    public function testPrimitive()
    {
        $proxy = new ProxyPrimitive('foo');

        $this->assertSame('foo', $proxy->popsPrimitive());
        $this->assertSame('foo', strval($proxy));

        $proxy = new ProxyPrimitive(1);

        $this->assertSame(1, $proxy->popsPrimitive());
        $this->assertSame('1', strval($proxy));
    }
}
