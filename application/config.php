<?php
/**
 * @package     Engine_Core
 * @version     $Id: config.php 7539 2010-10-04 04:41:38Z john $
 * @copyright   Copyright (c) 2008 Webligo Developments
 * @license     http://www.socialengine.net/license/
 */
exit();
//define('APPLICATION_ENV', 'production');

error_reporting(E_ALL);
define('_ENGINE', TRUE);
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

// Define path to application directory and application environment
// realpath() May cause problems on shared servers, although improves include speed
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));
defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');

// Check for database config (use this to do install later?)
defined('APPLICATION_NAME') || define('APPLICATION_NAME', (
  //file_exists(APPLICATION_PATH . '/application/settings/database.php') ? 'Core' : 'Install'
  'Core'
));

// Setup required include paths; optimized for Zend usage. Most other includes
// will use an absolute path
set_include_path(
  APPLICATION_PATH . DS . 'application' . DS . 'libraries' . PS .
  APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'PEAR' . PS .
  '.' // get_include_path()
);

// Application
require_once 'Engine/Loader.php';
require_once 'Engine/Application.php';

Engine_Loader::getInstance()
  // Libraries
  ->register('Zend', APPLICATION_PATH . DS . 'application' . DS .'libraries' . DS . 'Zend')
  ->register('Engine', APPLICATION_PATH . DS . 'application' . DS .'libraries' . DS . 'Engine')
  ->register('Facebook', APPLICATION_PATH . DS . 'application' . DS .'libraries' . DS . 'Facebook')
  // Plugins
  ->register('Plugin', APPLICATION_PATH . DS . 'application' . DS .'plugins')
  // Widgets
  ->register('Widget', APPLICATION_PATH . DS . 'application' . DS .'widgets')
;