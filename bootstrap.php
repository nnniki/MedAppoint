<?php

session_start();

spl_autoload_register(function (string $className)
{
    $includePaths = [
        "./",
    ];
    
    foreach ($includePaths as $path) {
        $file = "${path}/${className}.php";
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});
?>