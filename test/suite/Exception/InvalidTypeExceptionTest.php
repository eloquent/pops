<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class InvalidTypeExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $cause = new Exception;
        $exception = new InvalidTypeException('value', 'type', $cause);

        $this->assertSame('value', $exception->value());
        $this->assertSame('type', $exception->expectedType());
        $this->assertSame("Invalid value 'value'. Expected value of type 'type'.", $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertSame($cause, $exception->getPrevious());
    }
}
