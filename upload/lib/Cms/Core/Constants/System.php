<?php
// Current directory - /sys
$systemDir = dirname(__FILE__).'/../../../../sys/';

// Root directory
$rootDir = str_replace("/sys", "", $systemDir);

// Core constants
define('ABS_ROOT', $rootDir);
define('ABS_SYS_ROOT', $systemDir);

// Hostname
if (isset($_SERVER['HTTP_HOST'])) {
    define('SVR_HOST', $_SERVER['HTTP_HOST']);
} else {
    define('SVR_HOST', '/');
}

// Root path MUST include a trailing forward slash!
define('URL_ROOT', "/");

// This stops template constants from being parsed
define('ZZZ_TEMP', 'XXX---IJ-PLACEHOLDER-TEXT---XXX');
