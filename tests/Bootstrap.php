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

// Test namespaces:
$loader = require $file;
$loader->add('Phpro\DoctrineHydrationModule\Tests', __DIR__);
$loader->add('Phpro\DoctrineHydrationModule\Fixtures', __DIR__);

// Base tests:
require_once(__DIR__ . '/../vendor/doctrine/mongodb-odm/tests/Doctrine/ODM/MongoDB/Tests/BaseTest.php');

// Constants
define('TEST_BASE_PATH', __DIR__);
define('DOCTRINE_MONGODB_DATABASE', 'hydrator-tests');
define('DOCTRINE_MONGODB_SERVER', 'mongodb://localhost:27017');

// Load annotated classes
\Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::registerAnnotationClasses();
