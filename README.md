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

    class Confusion()
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

    use Ezzatron\Pops\Access\ProxyObject;

    $object = new SeriousBusiness;
    $proxy = ProxyObject::proxy($object);

    echo $proxy->foo('not so private...');   // outputs 'foo is not so private...'
    echo $proxy->bar.' = blown';             // outputs 'mind = blown'

#### For classes

The same concept applies for static methods and properties:

    class SeriousBusiness
    {
      static private function foo($adjective) { return 'foo is '.$adjective; }
      static private $bar = 'mind';
    }

To access these, a **class proxy** must be used instead of an **object proxy**,
but they operate in a similar manner:

    use Ezzatron\Pops\Access\ProxyClass;

    $proxy = ProxyClass::proxy('SeriousBusiness');

    echo $proxy->foo('not so private...');   // outputs 'foo is not so private...'
    echo $proxy->bar.' = blown';             // outputs 'mind = blown'

Alternatively, Pops can generate a class that can be used statically:

    use Ezzatron\Pops\Access\ProxyClass;

    $proxyClass = ProxyClass::proxyClass('SeriousBusiness');

    echo $proxyClass::foo('not so private...');       // outputs 'foo is not so private...'
    echo $proxyClass::_popsProxy()->bar.' = blown';   // outputs 'mind = blown'

Unfortunately, there is (currently) no __getStatic() or setStatic() in PHP, so
accessing static properties in this way is a not as elegant as it could be.

#### Access proxy applications

* Writing [white-box](http://en.wikipedia.org/wiki/White-box_testing) style unit
  tests (testing protected/private methods).
* Modifying behaviour of poorly designed third-party libraries.

### Code quality

Pops strives to attain a high level of quality. A full test suite is available,
and code coverage is closely monitored.

#### Latest revision test suite results
http://ci.ezzatron.com/pops

#### Latest revision test suite coverage
http://ci.ezzatron.com/report/pops/coverage/