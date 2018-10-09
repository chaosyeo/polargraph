#!/usr/bin/env php
<?php

require __DIR__ . '/../src/bootstrap.php';

use Polargraph\Compiler;

if (PHP_SAPI !== 'cli') {
    echo 'Warning: Polargraph Compiler should be invoked via the CLI version of PHP, not the ' . PHP_SAPI . ' SAPI' . PHP_EOL;
}
if(function_exists('ini_set')) {
    ini_set('phar.readonly', 'off');
    @ini_set('display_errors', 1);
    @ini_set('memory_limit', '512M');
}

try {
    $compiler = new Compiler();
    $compiler->execute();
} catch(Exception $e) {
    echo 'Failed to compile phar: [', get_class($e), '] ', $e->getMessage(), ' at ', $e->getFile(), ':', $e->getLine(), PHP_EOL;
    exit();
}

