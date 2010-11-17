<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.php 7533 2010-10-02 09:42:49Z john $
 * @author     John
 */

// Sanity check
if( version_compare(PHP_VERSION, '5.1.2', '<') ) {
  echo 'SocialEngine requires at least PHP 5.1.2';
  exit();
}

// Redirect to index.php if rewrite not enabled
$target = null;
if( empty($_GET['rewrite']) && $_SERVER['PHP_SELF'] != parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ) {
  $target = $_SERVER['PHP_SELF'];
  $params = $_GET;
  unset($params['rewrite']);
  if( !empty($params) ) {
    $target .= '?' . http_build_query($params);
  }
} else if( isset($_GET['rewrite']) && $_GET['rewrite'] == 2 ) {
  //$target = dirname($_SERVER['PHP_SELF']);
  $target = preg_replace('/\/index\.php\/?/i', '/', $_SERVER['REQUEST_URI']);
}
if( null !== $target ) {
  header('Location: ' . $target);
  exit();
}

error_reporting(E_ALL);
define('_ENGINE', TRUE);
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

define('_ENGINE_REQUEST_START', microtime(true));

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(__FILE__))));

defined('APPLICATION_ENV') || (
  !empty($_SERVER['_ENGINE_ENVIRONMENT']) && in_array($_SERVER['_ENGINE_ENVIRONMENT'], array('development', 'staging', 'production')) ?
  define('APPLICATION_ENV', $_SERVER['_ENGINE_ENVIRONMENT']) :
  define('APPLICATION_ENV', 'production')
);

defined('_ENGINE_NO_AUTH') || (
  !empty($_SERVER['_ENGINE_NOAUTH']) && $_SERVER['_ENGINE_NOAUTH'] == '1' ?
  define('_ENGINE_NO_AUTH', true) :
  define('_ENGINE_NO_AUTH', false)
);

set_include_path(
  APPLICATION_PATH . DS . 'application' . DS . 'libraries' . PS .
  APPLICATION_PATH . DS . 'application' . DS . 'libraries' . DS . 'PEAR' . PS .
  '.' // get_include_path()
);

require_once "Zend/Loader.php";
require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Engine');

$application = new Zend_Application(APPLICATION_ENV, array(
  'bootstrap' => array(
    'class' => 'Install_Bootstrap',
    'path' => APPLICATION_PATH . '/install/Bootstrap.php',
  ),
));


// Debug
if( !empty($_SERVER['_ENGINE_TRACE_ALLOW']) && extension_loaded('xdebug') ) {
  xdebug_start_trace();
}


// Run
try {
  $application->bootstrap();
  $application->run();
} catch( Exception $e ) {

  // Render custom error page
  $error = $e;
  $base = dirname($_SERVER['PHP_SELF']);
  include_once './views/scripts/_rawError.tpl';
}

// Debug
if( !empty($_SERVER['_ENGINE_TRACE_ALLOW']) && extension_loaded('xdebug') ) {
  xdebug_stop_trace();
}