<?php

// include fixtures than cannot be autoloaded
$documentationFixturePath =
    __DIR__.'/src/Eloquent/Pops/Test/Fixture/Documentation'
;
require $documentationFixturePath.'/Confusion.php';
require $documentationFixturePath.'/OutputEscaper.php';
require $documentationFixturePath.'/UppercaseProxyObject.php';
