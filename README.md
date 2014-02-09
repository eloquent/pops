# Pops

*PHP Object Proxy System.*

[![The most recent stable version is 4.1.0][version-image]][Semantic versioning]
[![Current build status image][build-image]][Current build status]
[![Current coverage status image][coverage-image]][Current coverage status]

## Installation and documentation

* Available as [Composer] package [eloquent/pops].
* [API documentation] available.

## What is *Pops*?

*Pops* is a system for wrapping PHP objects in other objects to modify their
behaviour. A *Pops* proxy will, as much as possible, imitate the object it
wraps. It passes along method calls and returns the underlying result, and
allows transparent access to properties (for both setting and getting).

Pops is the underlying system behind [Liberator].

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

We use `popsCall()` here rather than `__call()` to get around PHP limitations to
do with passing arguments by reference. See [calling methods with by-reference
parameters] for a deeper explanation.

Now when we access `wat()` and `$derp` both normally, and through our proxy, we
can see the effect:

```php
$confusion = new Confusion;
$proxy = new UppercaseProxyObject($confusion);

echo $confusion->wat(); // outputs "What is this? I don't even..."
echo $proxy->wat();     // outputs "WHAT IS THIS? I DON'T EVEN..."

echo $confusion->derp;  // outputs 'Has anyone really been far even as decided to use even?'
echo $proxy->derp;      // outputs 'HAS ANYONE REALLY BEEN FAR EVEN AS DECIDED TO USE EVEN?'
```

## Recursive proxies

*Pops* proxies can be applied to any value recursively. This comes in handy when
designing, for example, an output escaper (similar to Symfony). Here's an
example of how such a system could be created for escaping HTML output:

```php
namespace OutputEscaper;

use Eloquent\Pops\Proxy;

/**
 * Escapes output for use in HTML.
 */
class OutputEscaperProxy extends Proxy
{
    /**
     * Get the array proxy class.
     *
     * @return string The array proxy class.
     */
    protected static function proxyArrayClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyArray';
    }

    /**
     * Get the class proxy class.
     *
     * @return string The class proxy class.
     */
    protected static function proxyClassClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyClass';
    }

    /**
     * Get the object proxy class.
     *
     * @return string The object proxy class.
     */
    protected static function proxyObjectClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyObject';
    }

    /**
     * Get the proxy class for primitive values.
     *
     * @return string The proxy class for primitive values.
     */
    protected static function proxyPrimitiveClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxyPrimitive';
    }
}
```

```php
namespace OutputEscaper;

use Eloquent\Pops\ProxyArray;

/**
 * Wraps an array to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyArray extends ProxyArray
{
    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxy';
    }
}
```

```php
namespace OutputEscaper;

use Eloquent\Pops\ProxyClass;

/**
 * Wraps a class to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyClass extends ProxyClass
{
    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxy';
    }
}
```

```php
namespace OutputEscaper;

use Eloquent\Pops\ProxyObject;

/**
 * Wraps an object to escape any sub-values for use in HTML.
 */
class OutputEscaperProxyObject extends ProxyObject
{
    /**
     * Get the proxy class.
     *
     * @return string The proxy class.
     */
    protected static function popsProxyClass()
    {
        return __NAMESPACE__ . '\OutputEscaperProxy';
    }
}
```

```php
namespace OutputEscaper;

use Eloquent\Pops\ProxyPrimitive;

/**
 * Wraps a primitive to escape its value for use in HTML.
 */
class OutputEscaperProxyPrimitive extends ProxyPrimitive
{
    /**
     * Get the HTML-escaped version of this primitive.
     *
     * @return string The HTML-secaped version of this primitive.
     */
    public function __toString()
    {
        return htmlspecialchars(
            strval($this->popsValue()),
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

$list = new ArrayIterator(
    array(
        'foo',
        'bar',
        '<script>alert(document.cookie);</script>',
        SafeProxy::proxy('<em>ooh...</em>'),
    )
);
$proxy = OutputEscaperProxy::proxy($list, true);

echo "<ul>\n";
foreach ($proxy as $item) {
    printf("<li>%s</li>\n", $item);
}
echo "</ul>\n";
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
proxy. When *Pops* applies its proxies, it will skip anything marked as safe in
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

This method cannot be proxied normally because the `$wasPhone` argument is
passed by reference. The correct way to call the above butWho() method through a
*Pops* proxy looks like this:

```php
use Eloquent\Pops\Proxy;

$proxy = Proxy::proxy(new Confusion);

$wasPhone = null;
$arguments = array(&$wasPhone);

$proxy->popsCall('butWho', $arguments);

echo $wasPhone; // outputs 'Hello? Yes this is dog.'
```

Note that there **must** be a variable for the `$wasPhone` argument, and there
**must** be a variable for the arguments themselves. Neither can be passed
directly as a value. The arguments must also contain a **reference** to
`$wasPhone` argument.

<!-- References -->

[calling methods with by-reference parameters]: #calling-methods-with-by-reference-parameters
[Liberator]: https://github.com/eloquent/liberator

[API documentation]: http://lqnt.co/pops/artifacts/documentation/api/
[Composer]: http://getcomposer.org/
[build-image]: http://img.shields.io/travis/eloquent/pops/develop.svg "Current build status for the develop branch"
[Current build status]: https://travis-ci.org/eloquent/pops
[coverage-image]: http://img.shields.io/coveralls/eloquent/pops/develop.svg "Current test coverage for the develop branch"
[Current coverage status]: https://coveralls.io/r/eloquent/pops
[eloquent/pops]: https://packagist.org/packages/eloquent/pops
[Semantic versioning]: http://semver.org/
[version-image]: http://img.shields.io/:semver-4.1.0-brightgreen.svg "This project uses semantic versioning"
