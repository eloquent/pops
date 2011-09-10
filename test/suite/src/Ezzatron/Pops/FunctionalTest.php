<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ArrayIterator;
use Ezzatron\Pops\Access\Pops;
use Ezzatron\Pops\Safe\Pops as Safe;
use Ezzatron\Pops\Test\TestCase;
use OutputEscaper\Pops as OutputEscaper;

class FunctionalTest extends TestCase
{
  public function testDocumentationUppercaseProxy()
  {
    $confusion = new Confusion;
    $proxy = new UppercaseProxy($confusion);

    $this->assertEquals("What is this? I don't even...", $confusion->wat());
    $this->assertEquals("WHAT IS THIS? I DON'T EVEN...", $proxy->wat());

    $this->assertEquals('Has anyone really been far even as decided to use even go want to do look more like?', $confusion->derp);
    $this->assertEquals('HAS ANYONE REALLY BEEN FAR EVEN AS DECIDED TO USE EVEN GO WANT TO DO LOOK MORE LIKE?', $proxy->derp);
  }

  public function testDocumentationAccessProxyObject()
  {
    $object = new SeriousBusiness;
    $proxy = Pops::proxy($object);

    $this->assertEquals('foo is not so private...', $proxy->foo('not so private...'));
    $this->assertEquals('mind = blown', $proxy->bar.' = blown');
  }

  public function testDocumentationAccessProxyClass()
  {
    $proxy = Pops::proxyClass('SeriousBusiness');

    $this->assertEquals('baz is not so private...', $proxy->baz('not so private...'));
    $this->assertEquals('mind = blown', $proxy->qux.' = blown');
  }

  public function testDocumentationAccessProxyClassStatic()
  {
    $proxyClass = Pops::proxyClassStatic('SeriousBusiness');

    $this->assertEquals('baz is not so private...', $proxyClass::baz('not so private...'));
    $this->assertEquals('mind = blown', $proxyClass::_popsProxy()->qux.' = blown');
  }

  public function testDocumentationOutputEscaper()
  {
    $list = new ArrayIterator(array(
      'foo',
      'bar',
      '<script>alert(document.cookie);</script>',
      Safe::proxy('<em>ooh...</em>'),
    ));
    $proxy = OutputEscaper::proxy($list, true);

    $expected =
      '<ul>'.PHP_EOL
      .'<li>foo</li>'.PHP_EOL
      .'<li>bar</li>'.PHP_EOL
      .'<li>&lt;script&gt;alert(document.cookie);&lt;/script&gt;</li>'.PHP_EOL
      .'<li><em>ooh...</em></li>'.PHP_EOL
      .'</ul>'
    ;

    ob_start();
    echo '<ul>'.PHP_EOL;
    foreach ($proxy as $item)
    {
      echo '<li>'.$item.'</li>'.PHP_EOL;
    }
    echo '</ul>';
    $actual = ob_get_clean();

    $this->assertEquals($expected, $actual);
  }
}