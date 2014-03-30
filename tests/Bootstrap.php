<?php
/*
 * Set error reporting
 */
error_reporting( E_ALL | E_STRICT );

/*
 * Autoloading
 */
if (!file_exists($file = __DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException('Install dependencies to run test suite.');
}
$loader = require $file;
$loader->add('Phpro\DoctrineHydrationModule\Tests', __DIR__);

// Constants
define('TEST_BASE_PATH', __DIR__);
