<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops\Access;

use Eloquent\Pops\ProxyClass;
use LogicException;
use ReflectionClass;

class AccessProxyClass extends ProxyClass
{
    /**
     * @param string $class
     * @param boolean $recursive
     */
    public function __construct($class, $recursive = null)
    {
        parent::__construct($class, $recursive);

        $this->popsReflector = new ReflectionClass($class);
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        if (method_exists($this->popsClass, $method)) {
            $method = $this->popsReflector->getMethod($method);
            $method->setAccessible(true);

            return static::popsProxySubValue(
                $method->invokeArgs(null, $arguments),
                $this->popsRecursive
            );
        }

        return parent::__call($method, $arguments);
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            $propertyReflector->setValue(null, $value);

            return;
        }

        throw new LogicException(
            'Access to undeclared static property: '.
            $this->popsClass.
            '::$'.
            $property
        );
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            return static::popsProxySubValue(
                $propertyReflector->getValue(null),
                $this->popsRecursive
            );
        }

        throw new LogicException(
            'Access to undeclared static property: '.
            $this->popsClass.
            '::$'.
            $property
        );
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            return null !== $propertyReflector->getValue(null);
        }

        return parent::__isset($property);
    }

    /**
     * @param string $property
     */
    public function __unset($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            $propertyReflector->setValue(null, null);

            return;
        }

        throw new LogicException(
            'Access to undeclared static property: '.
            $this->popsClass.
            '::$'.
            $property
        );
    }

    /**
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\AccessProxy';
    }

    /**
     * @param string $property
     *
     * @return ReflectionProperty|null
     */
    protected function popsPropertyReflector($property)
    {
        if (property_exists($this->popsClass, $property)) {
            $property = $this->popsReflector->getProperty($property);
            $property->setAccessible(true);

            return $property;
        }

        return null;
    }

    /**
     * @var ReflectionClass
     */
    protected $popsReflector;
}
