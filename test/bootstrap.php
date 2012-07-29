<?php

$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('Eloquent', __DIR__.'/src');

// include fixtures than cannot be autoloaded
$documentationFixturePath =
    __DIR__.'/src/Eloquent/Pops/Test/Fixture/Documentation'
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
