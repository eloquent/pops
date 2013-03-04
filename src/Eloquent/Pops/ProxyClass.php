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

use InvalidArgumentException;
use LogicException;
use ReflectionClass;

class ProxyClass implements Proxy
{
    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic($method, array $arguments)
    {
        return static::popsProxySubValue(
            call_user_func_array(
                array(static::popsProxy(), $method),
                $arguments
            ),
            static::$popsStaticRecursive
        );
    }

    /**
     * @return ProxyClass
     */
    public static function popsProxy()
    {
        $originalClass = static::$popsStaticOriginalClass;
        if (null === $originalClass) {
            throw new LogicException(
                'This class should not be called directly.'
            );
        }

        $proxyClassClass = get_called_class();

        if (
            !isset(self::$popsStaticProxies[$proxyClassClass][$originalClass])
        ) {
            self::$popsStaticProxies[$proxyClassClass][$originalClass]
                = new $proxyClassClass($originalClass)
            ;
        }

        return self::$popsStaticProxies[$proxyClassClass][$originalClass];
    }

    /**
     * @param string $class
     * @param boolean $recursive
     * @param string $proxyClass
     *
     * @return string
     */
    public static function popsGenerateStaticClassProxy(
        $class,
        $recursive = null,
        $proxyClass = null
    ) {
        if (null === $recursive) {
            $recursive = false;
        }
        if (!is_bool($recursive)) {
            throw new InvalidArgumentException(
                'Provided value is not a boolean'
            );
        }

        $classDefinition = static::popsStaticClassProxyDefinition(
            $class,
            $recursive,
            $proxyClass
        );
        eval($classDefinition);

        return $proxyClass;
    }

    /**
     * @param string $class
     * @param boolean $recursive
     */
    public function __construct($class, $recursive = null)
    {
        if (!is_string($class)) {
            throw new InvalidArgumentException(
                'Provided value is not a string'
            );
        }

        if (null === $recursive) {
            $recursive = false;
        }
        if (!is_bool($recursive)) {
            throw new InvalidArgumentException(
                'Provided value is not a boolean'
            );
        }

        $this->popsClass = $class;
        $this->popsRecursive = $recursive;
    }

    /**
     * @return string
     */
    public function popsClass()
    {
        return $this->popsClass;
    }

    /**
     * @param string $method
     * @param array &$arguments
     *
     * @return mixed
     */
    public function popsCall($method, array &$arguments)
    {
        return static::popsProxySubValue(
            call_user_func_array($this->popsClass.'::'.$method, $arguments),
            $this->popsRecursive
        );
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments)
    {
        return $this->popsCall($method, $arguments);
    }

    /**
     * @param string $property
     * @param mixed $value
     */
    public function __set($property, $value)
    {
        $class = $this->popsClass;
        $class::$$property = $value;
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        $class = $this->popsClass;

        return static::popsProxySubValue(
            $class::$$property,
            $this->popsRecursive
        );
    }

    /**
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        $class = $this->popsClass;

        return isset($class::$$property);
    }

    /**
     * @param string $property
     */
    public function __unset($property)
    {
        $class = $this->popsClass;

        $class::$$property = null;
    }

    /**
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\Pops';
    }

    /**
     * @param mixed $value
     * @param boolean $recursive
     *
     * @return mixed
     */
    protected static function popsProxySubValue($value, $recursive)
    {
        if ($recursive) {
            $popsClass = static::popsProxyClass();

            return $popsClass::proxy($value, true);
        }

        return $value;
    }

    /**
     * @param string $originalClass
     * @param boolean $recursive
     * @param string $proxyClass
     *
     * @return string
     */
    protected static function popsStaticClassProxyDefinition(
        $originalClass,
        $recursive,
        &$proxyClass
    ) {
        $proxyClass = static::popsStaticClassProxyDefinitionProxyClass(
            $originalClass,
            $proxyClass
        );

        return
            static::popsStaticClassProxyDefinitionHeader(
                $proxyClass
            ).
            ' { '.
            static::popsStaticClassProxyDefinitionBody(
                $originalClass,
                $recursive
            ).
            ' }'
        ;
    }

    /**
     * @param string $originalClass
     * @param string $proxyClass
     *
     * @return string
     */
    protected static function popsStaticClassProxyDefinitionProxyClass(
        $originalClass,
        $proxyClass
    ) {
        if (null === $proxyClass) {
            $originalClassParts = explode('\\', $originalClass);
            $proxyClassPrefix = array_pop($originalClassParts).'_Pops_';
            $proxyClassNamespace = implode('\\', $originalClassParts);
            $proxyClass = uniqid($proxyClassPrefix);
            if ($proxyClassNamespace) {
                $proxyClass = $proxyClassNamespace.'\\'.$proxyClass;
            }
        }

        return $proxyClass;
    }

    /**
     * @param string $proxyClass
     *
     * @return string
     */
    protected static function popsStaticClassProxyDefinitionHeader($proxyClass)
    {
        $proxyClassParts = explode('\\', $proxyClass);
        $proxyClass = array_pop($proxyClassParts);
        $proxyClassNamespace = implode('\\', $proxyClassParts);

        $header = 'class '.$proxyClass.' extends \\'.get_called_class();
        if ($proxyClassNamespace) {
            $header = 'namespace '.$proxyClassNamespace.'; '.$header;
        }

        return $header;
    }

    /**
     * @param string $originalClass
     * @param boolean $recursive
     *
     * @return string
     */
    protected static function popsStaticClassProxyDefinitionBody(
        $originalClass,
        $recursive
    ) {
        return
            'protected static $popsStaticOriginalClass = '.
            var_export($originalClass, true).';'.
            ' protected static $popsStaticRecursive = '.
            var_export($recursive, true).';'
        ;
    }

    /**
     * @var string
     */
    protected static $popsStaticOriginalClass;

    /**
     * @var string
     */
    protected static $popsStaticRecursive;

    /**
     * @var array
     */
    protected static $popsStaticProxies = array();

    /**
     * @var string
     */
    protected $popsClass;

    /**
     * @var boolean
     */
    protected $popsRecursive;
}
