<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

use Eloquent\Pops\Proxy;
use Eloquent\Pops\Safe\SafeProxy;
use OutputEscaper\OutputEscaperProxy;

class FunctionalTest extends PHPUnit_Framework_TestCase
{
    public function testDocumentationUppercaseProxyObject()
    {
        $confusion = new Confusion;
        $proxy = new UppercaseProxyObject($confusion);

        $this->assertSame("What is this? I don't even...", $confusion->wat());
        $this->assertSame("WHAT IS THIS? I DON'T EVEN...", $proxy->wat());

        $this->assertSame('Has anyone really been far even as decided to use even?', $confusion->derp);
        $this->assertSame('HAS ANYONE REALLY BEEN FAR EVEN AS DECIDED TO USE EVEN?', $proxy->derp);
    }

    public function testDocumentationOutputEscaper()
    {
        $list = new ArrayIterator(array(
            'foo',
            'bar',
            '<script>alert(document.cookie);</script>',
            SafeProxy::proxy('<em>ooh...</em>'),
        ));
        $proxy = OutputEscaperProxy::proxy($list, true);

        $expected = <<<'EOD'
<ul>
<li>foo</li>
<li>bar</li>
<li>&lt;script&gt;alert(document.cookie);&lt;/script&gt;</li>
<li><em>ooh...</em></li>
</ul>

EOD;

        ob_start();
        echo "<ul>\n";
        foreach ($proxy as $item) {
            printf("<li>%s</li>\n", $item);
        }
        echo "</ul>\n";
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }

    public function testDocumentationCallWithReference()
    {
        $proxy = Proxy::proxy(new Confusion);
        $wasPhone = null;
        $arguments = array(&$wasPhone);
        $proxy->popsCall('butWho', $arguments);

        $this->assertSame('Hello? Yes this is dog.', $wasPhone);
    }
}
