<?php
/**
 * PHPUnit Bootstrap
 */

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Load Composer autoloader
require BASE_PATH . '/vendor/autoload.php';

// Load configuration
require BASE_PATH . '/config/config.php';
require BASE_PATH . '/config/db.php';

// Set up test environment
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'chababsetif.iba';
