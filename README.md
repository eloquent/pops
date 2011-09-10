## Pops - PHP Object Proxy System

### Installation

Requires PHP > 5.3.

* `git clone git@github.com:ezzatron/pops.git`
* `require '/path/to/pops/src/include.php';`

### What is Pops?

Pops is a system for wrapping PHP objects in other objects to modify their
behaviour. Its main feature is the **access proxy** system, but it can be used
to create other types of proxies too.

A Pops proxy will, as much as possible, imitate the object it wraps. It passes
along method calls and returns the underlying result, and allows transparent
access to properties (for both setting and getting).

### A basic example

Let's write a simple proxy that converts everything to uppercase. Here we have a
class:

    class Confusion
    {
      public function wat()
      {
        return "What is this? I don't even...";
      }

      public $derp = 'Has anyone really been far even as decided to use even go want to do look more like?';
    }

And here is our proxy:

    use Ezzatron\Pops\ProxyObject;

    class UppercaseProxy extends ProxyObject
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

Now when we access `wat()` and `$derp` both normally, and through our proxy, we
can see the effect:

    $confusion = new Confusion;
    $proxy = new UppercaseProxy($confusion);

    echo $confusion->wat();   // outputs "What is this? I don't even..."
    echo $proxy->wat();       // outputs "WHAT IS THIS? I DON'T EVEN..."

    echo $confusion->derp;    // outputs 'Has anyone really been far even as decided to use even go want to do look more like?'
    echo $proxy->derp;        // outputs 'HAS ANYONE REALLY BEEN FAR EVEN AS DECIDED TO USE EVEN GO WANT TO DO LOOK MORE LIKE?'

### Access proxy

The access proxy allows access to **protected** and **private** methods and
properties of objects as if they were marked **public**. It can do so for both
objects and classes (i.e. static methods and properties).

#### For objects

Take the following class:

    class SeriousBusiness
    {
      private function foo($adjective) { return 'foo is '.$adjective; }
      private $bar = 'mind';
    }

Normally there is no way to call `foo()` or access `$bar` from outside the
`SeriousBusiness` class, but an **access proxy** allows this to be achieved:

    use Ezzatron\Pops\Access\Pops;

    $object = new SeriousBusiness;
    $proxy = Pops::proxy($object);

    echo $proxy->foo('not so private...');   // outputs 'foo is not so private...'
    echo $proxy->bar.' = blown';             // outputs 'mind = blown'

#### For classes

The same concept applies for static methods and properties:

    class SeriousBusiness
    {
      static private function baz($adjective) { return 'baz is '.$adjective; }
      static private $qux = 'mind';
    }

To access these, a **class proxy** must be used instead of an **object proxy**,
but they operate in a similar manner:

    use Ezzatron\Pops\Access\Pops;

    $proxy = Pops::proxyClass('SeriousBusiness');

    echo $proxy->baz('not so private...');   // outputs 'baz is not so private...'
    echo $proxy->qux.' = blown';             // outputs 'mind = blown'

Alternatively, Pops can generate a class that can be used statically:

    use Ezzatron\Pops\Access\Pops;

    $proxyClass = Pops::proxyClassStatic('SeriousBusiness');

    echo $proxyClass::baz('not so private...');       // outputs 'baz is not so private...'
    echo $proxyClass::_popsProxy()->qux.' = blown';   // outputs 'mind = blown'

Unfortunately, there is (currently) no __getStatic() or setStatic() in PHP, so
accessing static properties in this way is a not as elegant as it could be.

#### Access proxy applications

* Writing [white-box](http://en.wikipedia.org/wiki/White-box_testing) style unit
  tests (testing protected/private methods).
* Modifying behaviour of poorly designed third-party libraries.

### Recursive proxies

Pops proxies can be applied to any value recursively. This comes in handy when
designing, for example, an output escaper (similar to Symfony).

Here's an example of how such a system could be created for escaping HTML
output. The empty classes are required, so that Pops can detect the namespace
and wrap sub-values with the appropriate classes.

    namespace OutputEscaper;

    class Pops extends \Ezzatron\Pops\Pops {}
    class ProxyArray extends \Ezzatron\Pops\ProxyArray {}
    class ProxyClass extends \Ezzatron\Pops\ProxyClass {}
    class ProxyObject extends \Ezzatron\Pops\ProxyObject {}

    class ProxyPrimitive extends \Ezzatron\Pops\ProxyPrimitive
    {
      public function __toString()
      {
        return htmlspecialchars((string)$this->_popsPrimitive, ENT_QUOTES, 'UTF-8');
      }
    }

The output escaper can now be used like so:

    use OutputEscaper\Pops as OutputEscaper;
    use Ezzatron\Pops\Safe\Pops as Safe;

    $list = new ArrayIterator(array(
      'foo',
      'bar',
      '<script>alert(document.cookie);</script>',
      Safe::proxy('<em>ooh...</em>'),
    ));
    $proxy = OutputEscaper::proxy($list, true);

    echo '<ul>'.PHP_EOL;
    foreach ($proxy as $item)
    {
      echo '<li>'.$item.'</li>'.PHP_EOL;
    }
    echo '</ul>';

Which would output:

    <ul>
    <li>foo</li>
    <li>bar</li>
    <li>&lt;script&gt;alert(document.cookie);&lt;/script&gt;</li>
    <li><em>ooh...</em></li>
    </ul>

Note that the above example should **NOT** be used in production. Output
escaping is a complex issue that should not be taken lightly.

#### Excluding values from recursion

Note that in the above example, the last list item was wrapped in a *Safe*
proxy. When Pops applies its proxies, it will skip anything marked as safe in
this manner.

### Code quality

Pops strives to attain a high level of quality. A full test suite is available,
and code coverage is closely monitored. All of the above code examples are also
tested.

#### Latest revision test suite results
http://ci.ezzatron.com/pops

#### Latest revision test suite coverage
http://ci.ezzatron.com/report/pops/coverage/