<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pops;

use LogicException;

/**
 * A transparent class proxy.
 */
class ProxyClass extends AbstractProxy implements ProxyClassInterface
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
            static::$isPopsStaticRecursive
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
     * @param string       $class       The name of the class to proxy.
     * @param boolean|null $isRecursive True if the proxy should be recursive.
     * @param string|null  $proxyClass  The class name to use for the proxy class.
     *
     * @return string The class name used for the proxy class.
     */
    public static function popsGenerateStaticClassProxy(
        $class,
        $isRecursive = null,
        $proxyClass = null
    ) {
        if (null === $isRecursive) {
            $isRecursive = false;
        }

        $classDefinition = static::popsStaticClassProxyDefinition(
            $class,
            $isRecursive,
            $proxyClass
        );
        eval($classDefinition);

        return $proxyClass;
    }

    /**
     * Construct a new non-static class proxy.
     *
     * @param string       $class       The name of the class to proxy.
     * @param boolean|null $isRecursive True if the proxy should be recursive.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    public function __construct($class, $isRecursive = null)
    {
        if (null === $isRecursive) {
            $isRecursive = false;
        }

        parent::__construct($class);

        $this->isPopsRecursive = $isRecursive;
    }

    /**
     * Get the name of the proxied class.
     *
     * @deprecated Use popsValue() instead.
     * @see ProxyInterface::popsValue()
     *
     * @return string The proxied class name.
     */
    public function popsClass()
    {
        return $this->popsValue();
    }

    /**
     * Returns true if the wrapped class is recursively proxied.
     *
     * @return boolean True if the wrapped class is recursively proxied.
     */
    public function isPopsRecursive()
    {
        return $this->isPopsRecursive;
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
            call_user_func_array(
                $this->popsValue() . '::' . $method,
                $arguments
            ),
            $this->isPopsRecursive()
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
        $class = $this->popsValue();
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
        $class = $this->popsValue();

        return static::popsProxySubValue(
            $class::$$property,
            $this->isPopsRecursive()
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
        $class = $this->popsValue();

        return isset($class::$$property);
    }

    /**
     * Set the value of a static property on the proxied class to null.
     *
     * @param string $property The name of the property to set.
     */
    public function __unset($property)
    {
        $class = $this->popsValue();

        $class::$$property = null;
    }

    /**
     * Get the string representation of this value.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return $this->popsValue();
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
     * @param mixed   $value       The value to wrap.
     * @param boolean $isRecursive True if recursive proxying is enabled.
     *
     * @return mixed The proxied value, or the untouched value.
     */
    protected static function popsProxySubValue($value, $isRecursive)
    {
        if ($isRecursive) {
            $popsProxyClass = static::popsProxyClass();

            return $popsProxyClass::proxy($value, true);
        }

        return $value;
    }

    /**
     * Generate a static class proxy definition.
     *
     * @param string      $class       The name of the class to proxy.
     * @param boolean     $isRecursive True if the proxy should be recursive.
     * @param string|null &$proxyClass The class name to use for the proxy class.
     *
     * @return string The proxy class definition.
     */
    protected static function popsStaticClassProxyDefinition(
        $originalClass,
        $isRecursive,
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
                $isRecursive
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
     * @param boolean $isRecursive   True if the proxy should be recursive.
     *
     * @return string The static class proxy class body.
     */
    protected static function popsStaticClassProxyDefinitionBody(
        $originalClass,
        $isRecursive
    ) {
        return sprintf(
            'protected static $popsStaticOriginalClass = %s; ' .
                'protected static $isPopsStaticRecursive = %s;',
            var_export($originalClass, true),
            var_export($isRecursive, true)
        );
    }

    /**
     * Throw an exception if the supplied value is an incorrect type for this
     * proxy.
     *
     * @param mixed $value The value to wrap.
     *
     * @throws Exception\InvalidTypeException If the supplied value is not the correct type.
     */
    protected function assertPopsValue($value)
    {
        if (!class_exists($value)) {
            throw new Exception\InvalidTypeException($value, 'class name');
        }
    }

    protected static $popsStaticOriginalClass;
    protected static $isPopsStaticRecursive;
    private static $popsStaticProxies = array();
    private $isPopsRecursive;
}
