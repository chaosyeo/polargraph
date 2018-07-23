<?php

function includeIfExists($file)
{
    if(file_exists($file)) {
        return include_once($file);
    }
    return false;
}

$loader = includeIfExists(__DIR__ . '/../vendor/autoload.php');
if($loader === false) {
    echo 'You must set up the project dependencies using `composer install`' . PHP_EOL;
    echo 'See https://getcomposer.org/download/ for instructions on installing Composer' . PHP_EOL;
    exit();
}
return $loader;