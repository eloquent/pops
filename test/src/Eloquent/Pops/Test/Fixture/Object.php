<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Test\Fixture;

class Object
{
    public static function staticPublicMethod()
    {
        return array(__FUNCTION__, func_get_args());
    }

    public static function __callStatic($name, array $arguments)
    {
        return array(__FUNCTION__, func_get_args());
    }

    public static function staticObject()
    {
        return new static;
    }

    public static function staticArray()
    {
        return array();
    }

    public static function staticString()
    {
        return 'string';
    }

    public static function staticByReference(&$variable, $value)
    {
        $variable = $value;
    }

    public function publicMethod()
    {
        return array(__FUNCTION__, func_get_args());
    }

    public function __call($method, array $arguments)
    {
        return array(__FUNCTION__, func_get_args());
    }

    public function object()
    {
        return new static;
    }

    public function arrayValue()
    {
        return array();
    }

    public function string()
    {
        return 'string';
    }

    public function byReference(&$variable, $value)
    {
        $variable = $value;
    }

    public static $staticPublicProperty = 'staticPublicProperty';

    public $publicProperty = 'publicProperty';
}
