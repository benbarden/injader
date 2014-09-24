<?php
// Current directory - /sys
$systemDir = dirname(__FILE__).'/../../../../sys/';

// Root directory
$rootDir = str_replace("/sys", "", $systemDir);

// Core constants
define('ABS_ROOT', $rootDir);
define('ABS_SYS_ROOT', $systemDir);
