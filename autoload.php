<?php

function register($path, $file)
{
    spl_autoload_register(function ($file) use ($path) {
        $file = str_replace('\\', DIRECTORY_SEPARATOR, $file).'.php';
        $name = array_slice(explode(DIRECTORY_SEPARATOR, $file), -1)[0];
        $realpath = $path.DIRECTORY_SEPARATOR.$name;
        if (file_exists($realpath) && !class_exists(substr($name, 0, -4), false)) {
            require($realpath);
            return true;
        }
        return false;
    }, false, true);
}

function load($path)
{
    $files = array_diff(scandir($path), array('.', '..', 'autoload.php', 'Demo.php', 'DemoImg'));

    if (!in_array('Dumper.php', $files)) {
        return ;
    }
    foreach ($files as $value) {
        register($path, $value);
    }
}

load(__DIR__);
