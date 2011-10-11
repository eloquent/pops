<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2011 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// path constants
require __DIR__.'/paths.php';

// include Pops
require POPS_SRC_DIR.DIRECTORY_SEPARATOR.'include.php';

// include test fixtures
require POPS_TEST_SRC_DIR.DIRECTORY_SEPARATOR.'include.php';