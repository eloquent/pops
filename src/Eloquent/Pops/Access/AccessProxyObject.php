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

use Eloquent\Pops\ProxyObject;
use ReflectionObject;

class AccessProxyObject extends ProxyObject
{
    /**
     * @param object $object
     * @param boolean $recursive
     */
    public function __construct($object, $recursive = null)
    {
        parent::__construct($object, $recursive);

        $this->popsReflector = new ReflectionObject($object);
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        if (method_exists($this->popsObject, $method)) {
            $method = $this->popsReflector->getMethod($method);
            $method->setAccessible(true);

            return $this->popsProxySubValue(
                $method->invokeArgs($this->popsObject, $arguments)
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
            $propertyReflector->setValue($this->popsObject, $value);

            return;
        }

        parent::__set($property, $value);
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            return $this->popsProxySubValue(
                $propertyReflector->getValue($this->popsObject)
            );
        }

        return parent::__get($property);
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            return null !== $propertyReflector->getValue($this->popsObject);
        }

        return parent::__isset($property);
    }

    /**
     * @param string $property
     */
    public function __unset($property)
    {
        if ($propertyReflector = $this->popsPropertyReflector($property)) {
            $propertyReflector->setValue($this->popsObject, null);

            return;
        }

        parent::__unset($property);
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
        if (property_exists($this->popsObject, $property)) {
            $property = $this->popsReflector->getProperty($property);
            $property->setAccessible(true);

            return $property;
        }

        return null;
    }

    /**
     * @var ReflectionObject
     */
    protected $popsReflector;
}
