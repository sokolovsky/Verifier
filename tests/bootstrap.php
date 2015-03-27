<?php
$directory = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'src';

include_once $directory.DIRECTORY_SEPARATOR.'Conditions.php';
include_once $directory.DIRECTORY_SEPARATOR.'Exceptions.php';

spl_autoload_register(function ($class) use ($directory) {
    if (!in_array(strpos($class, 'Verifier'), array(0, 1), true)) {
        return false;
    }
    $class = ltrim($class, '\\');
    $file = preg_replace('/^Verifier\\\\/', '', $class).'.php';
    include_once $directory.DIRECTORY_SEPARATOR.$file;
});
