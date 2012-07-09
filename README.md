# Pops

*PHP Object Proxy System.*

## Installation

Pops requires PHP 5.3 or later.

### With [Composer](http://getcomposer.org/)

* Add 'eloquent/pops' to your project's composer.json dependencies
* Run `php composer.phar install`

### Bare installation

* Clone from GitHub: `git clone git://github.com/eloquent/pops.git`
* Use a [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible autoloader (namespace 'Eloquent' in the 'src' directory)

## What is Pops?

Pops is a system for wrapping PHP objects in other objects to modify their
behaviour. Its main feature is the **access proxy** system, but it can be used
to create other types of proxies too.

A Pops proxy will, as much as possible, imitate the object it wraps. It passes
along method calls and returns the underlying result, and allows transparent
access to properties (for both setting and getting).

The most common usage of Pops is an access proxy to assist in [white-box](http://en.wikipedia.org/wiki/White-box_testing)
style unit testing.

## Access proxy

The access proxy allows access to **protected** and **private** methods and
properties of objects as if they were marked **public**. It can do so for both
objects and classes (i.e. static methods and properties).

### For objects

Take the following class:

```php
<?php

class SeriousBusiness
{
    private function foo($adjective)
    {
        return 'foo is '.$adjective;
    }

    private $bar = 'mind';
}
```

Normally there is no way to call `foo()` or access `$bar` from outside the
`SeriousBusiness` class, but an **access proxy** allows this to be achieved:

```php
<?php

use Eloquent\Pops\Access\AccessProxy;

$object = new SeriousBusiness;
$proxy = AccessProxy::proxy($object);

echo $proxy->foo('not so private...');   // outputs 'foo is not so private...'
echo $proxy->bar.' = blown';             // outputs 'mind = blown'
```

### For classes

The same concept applies for static methods and properties:

```php
<?php

class SeriousBusiness
{
    static private function baz($adjective)
    {
        return 'baz is '.$adjective;
    }

    static private $qux = 'mind';
}
```

To access these, a **class proxy** must be used instead of an **object proxy**,
but they operate in a similar manner:

```php
<?php

use Eloquent\Pops\Access\AccessProxy;

$proxy = AccessProxy::proxyClass('SeriousBusiness');

echo $proxy->baz('not so private...');   // outputs 'baz is not so private...'
echo $proxy->qux.' = blown';             // outputs 'mind = blown'
```

Alternatively, Pops can generate a class that can be used statically:

```php
<?php

use Eloquent\Pops\Access\AccessProxy;

$proxyClass = AccessProxy::proxyClassStatic('SeriousBusiness');

echo $proxyClass::baz('not so private...');      // outputs 'baz is not so private...'
echo $proxyClass::popsProxy()->qux.' = blown';   // outputs 'mind = blown'
```

Unfortunately, there is (currently) no __getStatic() or __setStatic() in PHP,
so accessing static properties in this way is a not as elegant as it could be.

### Access proxy applications

* Writing [white-box](http://en.wikipedia.org/wiki/White-box_testing) style unit
  tests (testing protected/private methods).
* Modifying behaviour of poorly designed third-party libraries.

## Custom proxies

Let's write a simple proxy that converts everything to uppercase. Here we have a
class:

```php
<?php

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
<?php

use Eloquent\Pops\ProxyObject;

class UppercaseProxyObject extends ProxyObject
{
    public function __call($method, array $arguments)
    {
        return strtoupper(parent::__call($method, $arguments));
    }

    public function __get($property)
    {
        return strtoupper(parent::__get($property));
    }
}
```

Now when we access `wat()` and `$derp` both normally, and through our proxy, we
can see the effect:

```php
<?php

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
<?php

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
<?php

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

## Code quality

Pops strives to attain a high level of quality. A full test suite is available,
and code coverage is closely monitored. All of the above code examples are
also tested.

All code follows the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
coding standards to the degree that current [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer)
rulesets check.

### Latest revision test suite results
[![Build Status](https://secure.travis-ci.org/eloquent/pops.png)](http://travis-ci.org/eloquent/pops)

### Latest revision test suite coverage
<http://ci.ezzatron.com/report/pops/coverage/>
