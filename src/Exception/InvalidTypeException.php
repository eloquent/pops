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

/**
 * A value of an invalid type was supplied.
 */
final class InvalidTypeException extends Exception
{
    /**
     * Construct a new invalid type exception.
     *
     * @param mixed          $value        The supplied value.
     * @param string         $expectedType The expected type.
     * @param Exception|null $cause        The cause, if available.
     */
    public function __construct($value, $expectedType, Exception $cause = null)
    {
        $this->value = $value;
        $this->expectedType = $expectedType;

        parent::__construct(
            sprintf(
                'Invalid value %s. Expected value of type %s.',
                var_export($value, true),
                var_export($expectedType, true)
            ),
            0,
            $cause
        );
    }

    /**
     * Get the upplied value.
     *
     * @return mixed THe supplied value.
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * Get the expected type.
     *
     * @return string The expected type.
     */
    public function expectedType()
    {
        return $this->expectedType;
    }

    private $value;
    private $expectedType;
}
