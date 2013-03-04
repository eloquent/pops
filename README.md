# Pops

*PHP Object Proxy System.*

[![Build Status]](http://travis-ci.org/eloquent/pops)
[![Test Coverage]](http://eloquent-software.com/pops/artifacts/tests/coverage/)

## Installation

Available as [Composer](http://getcomposer.org/) package
[eloquent/pops](https://packagist.org/packages/eloquent/pops).

## What is Pops?

Pops is a system for wrapping PHP objects in other objects to modify their
behaviour.

A Pops proxy will, as much as possible, imitate the object it wraps. It passes
along method calls and returns the underlying result, and allows transparent
access to properties (for both setting and getting).

Pops is the underlying system behind [Liberator](https://github.com/eloquent/liberator).

## Creating proxies

Let's write a simple proxy that converts everything to uppercase. Here we have a
class:

```php
class Confusion
{
    public function wat()
    {
        return "What is this? I don't even...";
    }

    public $derp = 'Has anyone really been far even as decided to use even?';
}
```

And here is our proxy:

```php
use Eloquent\Pops\ProxyObject;

class UppercaseProxyObject extends ProxyObject
{
    public function popsCall($method, array &$arguments)
    {
        return strtoupper(parent::popsCall($method, $arguments));
    }

    public function __get($property)
    {
        return strtoupper(parent::__get($property));
    }
}
```

We use popsCall() here rather than __call() to get around PHP limitations to do
with passing arguments by reference. See [below](#calling-methods-with-by-reference-parameters)
for a depper explanation.

Now when we access `wat()` and `$derp` both normally, and through our proxy, we
can see the effect:

```php
$confusion = new Confusion;
$proxy = new UppercaseProxyObject($confusion);

echo $confusion->wat();   // outputs "What is this? I don't even..."
echo $proxy->wat();       // outputs "WHAT IS THIS? I DON'T EVEN..."

echo $confusion->derp;    // outputs 'Has anyone really been far even as decided to use even?'
echo $proxy->derp;        // outputs 'HAS ANYONE REALLY BEEN FAR EVEN AS DECIDED TO USE EVEN?'
```

## Recursive proxies

Pops proxies can be applied to any value recursively. This comes in handy when
designing, for example, an output escaper (similar to Symfony).

Here's an example of how such a system could be created for escaping HTML
output:

```php
namespace OutputEscaper;

use Eloquent\Pops\Pops;
use Eloquent\Pops\ProxyArray;
use Eloquent\Pops\ProxyClass;
use Eloquent\Pops\ProxyObject;
use Eloquent\Pops\ProxyPrimitive;

/**
 * Escapes output for use in HTML.
 */
class OutputEscaperProxy extends Pops
{
    /**
     * The class to use when proxying arrays.
     *
     * @return string
     */
    static protected function proxyArrayClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxyArray';
    }

    /**
     * The class to use when proxying classes.
     *
     * @return string
     */
    static protected function proxyClassClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxyClass';
    }

    /**
     * The class to use when proxying objects.
     *
     * @return string
     */
    static protected function proxyObjectClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxyObject';
    }

    /**
     * The class to use when proxying primitives.
     *
     * @return string
     */
    static protected function proxyPrimitiveClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxyPrimitive';
    }
}

/**
 * Wraps an array to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyArray extends ProxyArray
{
    /**
     * The class to use when proxying sub-values.
     *
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxy';
    }
}

/**
 * Wraps a class to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyClass extends ProxyClass
{
    /**
     * The class to use when proxying sub-values.
     *
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxy';
    }
}

/**
 * Wraps an object to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyObject extends ProxyObject
{
    /**
     * The class to use when proxying sub-values.
     *
     * @return string
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__.'\OutputEscaperProxy';
    }
}

/**
 * Wraps a primitive to escape its value for use in HTML.
 */
class OutputEscaperProxyPrimitive extends ProxyPrimitive
{
    /**
     * Returns the HTML-escaped version of this primitive.
     *
     * @return string
     */
    public function __toString()
    {
        return htmlspecialchars(
            (string) $this->popsPrimitive,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
```

The output escaper can now be used like so:

```php
use OutputEscaper\OutputEscaperProxy;
use Eloquent\Pops\Safe\SafeProxy;

$list = new ArrayIterator(array(
    'foo',
    'bar',
    '<script>alert(document.cookie);</script>',
    SafeProxy::proxy('<em>ooh...</em>'),
));
$proxy = OutputEscaperProxy::proxy($list, true);

echo '<ul>'.PHP_EOL;
foreach ($proxy as $item) {
    echo '<li>'.$item.'</li>'.PHP_EOL;
}
echo '</ul>';
```

Which would output:

```html
<ul>
<li>foo</li>
<li>bar</li>
<li>&lt;script&gt;alert(document.cookie);&lt;/script&gt;</li>
<li><em>ooh...</em></li>
</ul>
```

Note that the above example should **NOT** be used in production. Output
escaping is a complex issue that should not be taken lightly.

### Excluding values from recursion

Note that in the above example, the last list item was wrapped in a *Safe*
proxy. When Pops applies its proxies, it will skip anything marked as safe in
this manner.

## Calling methods with by-reference parameters

Because of PHP limitations, methods with arguments that are passed by reference
must be called in a special way.

To explain futher, let's assume our class from before also has a method which
accepts a reference:

```php
class Confusion
{
    public function butWho(&$wasPhone)
    {
        $wasPhone = 'Hello? Yes this is dog.';
    }
}
```

This method cannot be proxied normally because the $wasPhone argument is passed
by reference. The correct way to call the above butWho() method through a Pops
proxy looks like this:

```php
$proxy = Pops::proxy(new Confusion);

$wasPhone = null;
$arguments = array(&$wasPhone);

$proxy->popsCall('butWho', $arguments);

echo $wasPhone;   // outputs 'Hello? Yes this is dog.'
```

Note that there **must** be a variable for the $wasPhone argument, and there
**must** be a variable for the arguments themselves. Neither can be passed
directly as a value. The arguments must also contain a **reference** to
$wasPhone argument.

<!-- references -->
[Build Status]: https://raw.github.com/eloquent/pops/gh-pages/artifacts/images/icecave/regular/build-status.png
[Test Coverage]: https://raw.github.com/eloquent/pops/gh-pages/artifacts/images/icecave/regular/coverage.png
