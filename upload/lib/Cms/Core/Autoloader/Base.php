<?php

function injAutoloader($className)
{
    $filePath = str_replace('\\', '/', $className);
    $fullPath = sprintf('%slib/%s.php', ABS_ROOT, $filePath);
    if (file_exists($fullPath)) {
        require_once $fullPath;
    }
}
spl_autoload_register('injAutoloader');