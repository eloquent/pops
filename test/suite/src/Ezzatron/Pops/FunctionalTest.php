<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ezzatron\Pops;

use Confusion;
use Ezzatron\Pops\Access\Pops as PopsAccess;
use Ezzatron\Pops\Test\TestCase;
use SeriousBusiness;
use UppercaseProxy;

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
    $proxy = PopsAccess::proxy($object);

    $this->assertEquals('foo is not so private...', $proxy->foo('not so private...'));
    $this->assertEquals('mind = blown', $proxy->bar.' = blown');
  }

  public function testDocumentationAccessProxyClass()
  {
    $proxy = PopsAccess::proxyClass('SeriousBusiness');

    $this->assertEquals('baz is not so private...', $proxy->baz('not so private...'));
    $this->assertEquals('mind = blown', $proxy->qux.' = blown');
  }

  public function testDocumentationAccessProxyClassStatic()
  {
    $proxyClass = PopsAccess::proxyClassStatic('SeriousBusiness');

    $this->assertEquals('baz is not so private...', $proxyClass::baz('not so private...'));
    $this->assertEquals('mind = blown', $proxyClass::_popsProxy()->qux.' = blown');
  }
}