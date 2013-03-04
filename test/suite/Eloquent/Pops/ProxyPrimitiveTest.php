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

/**
 * @covers Eloquent\Pops\ProxyPrimitive
 */
class ProxyPrimitiveTest extends TestCase
{
    public function testPrimitive()
    {
        $proxy = new ProxyPrimitive('foo');

        $this->assertEquals('foo', $proxy->popsPrimitive());
        $this->assertEquals('foo', (string) $proxy);


        $proxy = new ProxyPrimitive(1);

        $this->assertEquals(1, $proxy->popsPrimitive());
        $this->assertEquals('1', (string) $proxy);
    }
}
