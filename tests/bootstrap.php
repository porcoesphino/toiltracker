<?php

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

// Ensure library/ is on include_path

// Add the PEAR library: /usr/lib/php/pear
$zendLibrary = realpath(APPLICATION_PATH . '/../library');
set_include_path(implode(PATH_SEPARATOR, array(
    $zendLibrary,
    get_include_path(),
    '/usr/lib/php/pear/'
)));

echo get_include_path();

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
