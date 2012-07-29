<?php

$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('Eloquent', __DIR__.'/src');

// include fixtures than cannot be autoloaded
$documentationFixturePath =
    __DIR__.'/src/Eloquent/Pops/Test/Fixture/Documentation'
;
require $documentationFixturePath.'/Confusion.php';
require $documentationFixturePath.'/OutputEscaper.php';
require $documentationFixturePath.'/SeriousBusiness.php';
require $documentationFixturePath.'/UppercaseProxyObject.php';
