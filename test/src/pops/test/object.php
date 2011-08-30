<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pops\Test;

class Object
{
  /**
   * @return string
   */
  static public function staticPublicMethod()
  {
    return 'staticPublicMethod';
  }
  
  /**
   * @return string
   */
  public function publicMethod()
  {
    return 'publicMethod';
  }
}