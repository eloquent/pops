<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class SeriousBusiness
{
  private function foo($adjective) { return 'foo is '.$adjective; }
  private $bar = 'mind';
  
  static private function baz($adjective) { return 'baz is '.$adjective; }
  static private $qux = 'mind';
}