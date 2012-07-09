<?php

/*
 * This file is part of the Pops package.
 *
 * Copyright © 2012 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// path constants
require __DIR__.'/paths.php';

// use Composer for autoloading
$autoloader = require LQNT_VENDOR_DIR.DIRECTORY_SEPARATOR.'autoload.php';
$autoloader->add('Eloquent', LQNT_TEST_SRC_DIR);

// include fixtures than cannot be autoloaded
$documentationFixturePath =
    LQNT_TEST_SRC_DIR.
    DIRECTORY_SEPARATOR.'Eloquent'.
    DIRECTORY_SEPARATOR.'Pops'.
    DIRECTORY_SEPARATOR.'Test'.
    DIRECTORY_SEPARATOR.'Fixture'.
    DIRECTORY_SEPARATOR.'Documentation'
;
require
    $documentationFixturePath.
    DIRECTORY_SEPARATOR.'Confusion.php'
;
require
    $documentationFixturePath.
    DIRECTORY_SEPARATOR.'OutputEscaper.php'
;
require
    $documentationFixturePath.
    DIRECTORY_SEPARATOR.'SeriousBusiness.php'
;
require
    $documentationFixturePath.
    DIRECTORY_SEPARATOR.'UppercaseProxyObject.php'
;
