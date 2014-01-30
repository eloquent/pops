<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pops;

use LogicException;

/**
 * A transparent class proxy.
 */
class ProxyClass implements ProxyInterface
{
    /**
     * Call a method on this class proxy.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
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
     * Get the non-static class proxy for this class.
     *
     * @return ProxyClass The non-static class proxy.
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
            self::$popsStaticProxies[$proxyClassClass][$originalClass] =
                new $proxyClassClass($originalClass);
        }

        return self::$popsStaticProxies[$proxyClassClass][$originalClass];
    }

    /**
     * Generate and load a static class proxy.
     *
     * @param string       $class      The name of the class to proxy.
     * @param boolean|null $recursive  True if the proxy should be recursive.
     * @param string|null  $proxyClass The class name to use for the proxy class.
     *
     * @return string The class name used for the procy class.
     */
    public static function popsGenerateStaticClassProxy(
        $class,
        $recursive = null,
        $proxyClass = null
    ) {
        if (null === $recursive) {
            $recursive = false;
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
     * Construct a new non-static class proxy.
     *
     * @param string       $class     The name of the class to proxy.
     * @param boolean|null $recursive True if the proxy should be recursive.
     */
    public function __construct($class, $recursive = null)
    {
        if (null === $recursive) {
            $recursive = false;
        }

        $this->popsClass = $class;
        $this->popsRecursive = $recursive;
    }

    /**
     * Get the name of the proxied class.
     *
     * @return string The proxied class name.
     */
    public function popsClass()
    {
        return $this->popsClass;
    }

    /**
     * Call a static method on the proxied class with support for by-reference
     * arguments.
     *
     * @param string $method     The name of the method to call.
     * @param array  &$arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function popsCall($method, array &$arguments)
    {
        return static::popsProxySubValue(
            call_user_func_array($this->popsClass . '::' . $method, $arguments),
            $this->popsRecursive
        );
    }

    /**
     * Call a static method on the proxied class.
     *
     * @param string $method    The name of the method to call.
     * @param array  $arguments The arguments.
     *
     * @return mixed The result of the method call.
     */
    public function __call($method, array $arguments)
    {
        return $this->popsCall($method, $arguments);
    }

    /**
     * Set the value of a static property on the proxied class.
     *
     * @param string $property The name of the property to set.
     * @param mixed  $value    The new value.
     */
    public function __set($property, $value)
    {
        $class = $this->popsClass;
        $class::$$property = $value;
    }

    /**
     * Get the value of a static property on the proxied class.
     *
     * @param string $property The name of the property to get.
     *
     * @return mixed The value of the property.
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
     * Returns true if the supplied static property exists on the proxied class.
     *
     * @param string $property The name of the property to search for.
     *
     * @return boolean True if the property exists.
     */
    public function __isset($property)
    {
        $class = $this->popsClass;

        return isset($class::$$property);
    }

    /**
     * Set the value of a static property on the proxied class to null.
     *
     * @param string $property The name of the property to set.
     */
    public function __unset($property)
    {
        $class = $this->popsClass;

        $class::$$property = null;
    }

    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\Proxy';
    }

    /**
     * Wrap a sub-value in a proxy if recursive proxying is enabled.
     *
     * @param mixed   $value     The value to wrap.
     * @param boolean $recursive True if recursive proxying is enabled.
     *
     * @return mixed The proxied value, or the untouched value.
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
     * Generate a static class proxy definition.
     *
     * @param string      $class       The name of the class to proxy.
     * @param boolean     $recursive   True if the proxy should be recursive.
     * @param string|null &$proxyClass The class name to use for the proxy class.
     *
     * @return string The proxy class definition.
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

        return sprintf(
            '%s { %s }',
            static::popsStaticClassProxyDefinitionHeader($proxyClass),
            static::popsStaticClassProxyDefinitionBody(
                $originalClass,
                $recursive
            )
        );
    }

    /**
     * Generate a static class proxy class name, or return the supplied name.
     *
     * @param string      $originalClass The name of the class being proxied.
     * @param string|null $proxyClass    The class name to use for the proxy class.
     *
     * @return string The proxy class name.
     */
    protected static function popsStaticClassProxyDefinitionProxyClass(
        $originalClass,
        $proxyClass
    ) {
        if (null === $proxyClass) {
            $originalClassParts = explode('\\', $originalClass);
            $proxyClassPrefix = array_pop($originalClassParts) . '_Pops_';
            $proxyClassNamespace = implode('\\', $originalClassParts);
            $proxyClass = uniqid($proxyClassPrefix);
            if ($proxyClassNamespace) {
                $proxyClass = sprintf(
                    '%s\\%s',
                    $proxyClassNamespace,
                    $proxyClass
                );
            }
        }

        return $proxyClass;
    }

    /**
     * Generate the class header for a static class proxy class.
     *
     * @param string $proxyClass The class name to use for the proxy class.
     *
     * @return string The static class proxy class header.
     */
    protected static function popsStaticClassProxyDefinitionHeader($proxyClass)
    {
        $proxyClassParts = explode('\\', $proxyClass);
        $proxyClass = array_pop($proxyClassParts);
        $proxyClassNamespace = implode('\\', $proxyClassParts);

        $header = sprintf(
            'class %s extends \\%s',
            $proxyClass,
            get_called_class()
        );
        if ($proxyClassNamespace) {
            $header = sprintf(
                'namespace %s; %s',
                $proxyClassNamespace,
                $header
            );
        }

        return $header;
    }

    /**
     * Generate the class body for a static class proxy class.
     *
     * @param string  $originalClass The name of the class being proxied.
     * @param boolean $recursive     True if the proxy should be recursive.
     *
     * @return string The static class proxy class body.
     */
    protected static function popsStaticClassProxyDefinitionBody(
        $originalClass,
        $recursive
    ) {
        return sprintf(
            'protected static $popsStaticOriginalClass = %s; ' .
                'protected static $popsStaticRecursive = %s;',
            var_export($originalClass, true),
            var_export($recursive, true)
        );
    }

    private static $popsStaticOriginalClass;
    private static $popsStaticRecursive;
    private static $popsStaticProxies = array();
    private $popsClass;
    private $popsRecursive;
}
