<?php

// For PHP 8, hide deprecation warnings. A new major version of this SDK will be released soon.
if (PHP_VERSION_ID > 80000) {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
} else {
    error_reporting(E_ALL);
}

require 'vendor/autoload.php';
