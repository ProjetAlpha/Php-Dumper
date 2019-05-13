<?php

function register($file)
{
    spl_autoload_register(function ($file) {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file).'.php';
        $realpath = array_slice(explode(DIRECTORY_SEPARATOR, $file), -1)[0];
        if (file_exists($realpath) && !class_exists(substr($realpath, 0, -4), false)) {
            require($realpath);
            return true;
        }
        return false;
    }, false, true);
}

function load()
{
    $files = array_diff(scandir('.'), array('.', '..', 'autoload.php', 'Demo.php'));
    foreach ($files as $value) {
        register($value);
    }
}
load();
