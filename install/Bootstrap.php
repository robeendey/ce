<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Bootstrap.php 7566 2010-10-06 00:18:16Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
  protected function _initMisc()
  {
    if( function_exists('date_default_timezone_set') ) {
      date_default_timezone_set('UTC');
    }
    if( function_exists('mb_internal_encoding') ) {
      mb_internal_encoding("UTF-8");
    }
  }
  
  protected function _initAutoloader()
  {
    $autoloader = new Zend_Application_Module_Autoloader(array(
      'namespace' => 'Install',
      'basePath' => APPLICATION_PATH . '/install',
    ));

    Zend_Registry::set('Autoloader', $autoloader);

    return $autoloader;
  }

  protected function _initCache()
  {
    try {

      $cache = Zend_Cache::factory('Core', 'File', array(
        'automatic_serialization' => true,
      ), array(
        'cache_dir' => APPLICATION_PATH . '/temporary/cache',
        'file_name_prefix' => 'EngineInstall',
      ));

      Zend_Registry::set('Cache', $cache);

      return $cache;
      
    } catch( Exception $e ) {
      
    }
  }

  protected function _initMemoryManager()
  {
    try {

      $memoryManager = Zend_Memory::factory('File', array(
        'cache_dir' => APPLICATION_PATH . '/temporary/cache',
        'file_name_prefix' => 'EngineInstallMemory',
      ));
      $memoryManager->setMemoryLimit(24 * 1024 * 1024);

      Zend_Registry::set('MemoryManager', $memoryManager);

      return $memoryManager;

    } catch( Exception $e ) {

    }
  }

  protected function _initFrontController()
  {
    $front = Zend_Controller_Front::getInstance();

    $front->addControllerDirectory(APPLICATION_PATH . '/install/controllers');

    return $front;
  }

  protected function _initRouter()
  {
    $front = $this->getContainer()->frontcontroller;
    $router = $front->getRouter();
    //$router = new Zend_Controller_Router_Rewrite();
    $router->addRoute('install', new Zend_Controller_Router_Route('install/:action', array(
      'controller' => 'install',
      'action' => 'license',
    ), array(
      'action' => 'license|sanity|ftp-info|ftp-perms|db-info|db-sanity|db-create|account|complete',
    )));
    $router->addRoute('manage', new Zend_Controller_Router_Route('manage/:action', array(
      'controller' => 'manage',
      'action' => 'index',
    ), array(
      'action' => 'index|ftp|upload|browse',
    )));
    $router->addRoute('sdk', new Zend_Controller_Router_Route('sdk/:action', array(
      'controller' => 'sdk',
      'action' => 'index',
    ), array(
      'action' => 'index|create|build|manage|delete',
    )));
    $router->addRoute('login', new Zend_Controller_Router_Route_Static('login', array(
      'controller' => 'auth',
      'action' => 'login',
    )));
    $router->addRoute('logout', new Zend_Controller_Router_Route_Static('logout', array(
      'controller' => 'auth',
      'action' => 'logout',
    )));
    return $router;
  }

  protected function _initLog()
  {
    $log = new Zend_Log();
    try {
      $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/install.log'));
    } catch( Exception $e ) {
      $log->addWriter(new Zend_Log_Writer_Null());
    }

    // Non-production
    if( APPLICATION_ENV !== 'production' ) {
      $log->addWriter(new Zend_Log_Writer_Firebug());
    }
    
    Zend_Registry::set('Zend_Log', $log);
    Engine_Api::registerErrorHandlers();

    if( 'production' != APPLICATION_ENV ) {
      Engine_Exception::setLog($log);
    }

    return $log;
  }

  protected function _initSession()
  {
    $name = 'en4_install';
    Zend_Session::setOptions(array(
      'name' => $name,
      'cookie_path' => substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/')+1),
      'cookie_lifetime' => 0, //86400,
      'gc_maxlifetime' => 86400,
      'remember_me_seconds' => 86400,
      'cookie_httponly' => false,
    ));
    session_name($name);
    
    // Session hack for fancy upload
    if( isset($_POST[session_name()]) ) {
      Zend_Session::setId($_POST[session_name()]);
    } else if( isset($_COOKIE[session_name()]) ) {
      Zend_Session::setId($_COOKIE[session_name()]);
    }
    
    // Start
    try {
      Zend_Session::start();
    } catch( Exception $e ) { // This will generally happen when weird data is saved during the install process
      if( Zend_Session::isStarted() ) {
        Zend_Session::destroy();
      }
      throw $e;
    }

    // Session binding
    $fixed = true;
    $namespace = new Zend_Session_Namespace('ZendSession');
    if( empty($namespace->ip) /* || empty($namespace->ua)*/ ) {
      $namespace->ip = $_SERVER['REMOTE_ADDR'];
      $namespace->ua = @$_SERVER['HTTP_USER_AGENT'];
    } else if( $namespace->ip != $_SERVER['REMOTE_ADDR'] /* || $namespace->ua != $_SERVER['HTTP_USER_AGENT']*/ ) {
      $fixed = false;
    }

    // Occaisonally regenerate the id if requesting with the original user agent
    /*
    if( empty($namespace->count) ) {
      $namespace->count = 1;
    } else if( $namespace->count < 10 ) {
      $namespace->count++;
    } else if( $namespace->ua == $_SERVER['HTTP_USER_AGENT'] ) {
      Zend_Session::regenerateId();
    }
    */

    if( !$fixed ) {
      Zend_Session::destroy();
      header('Location: ' . $_SERVER['REQUEST_URI']);
      exit();
    }
  }

  protected function _initLayout()
  {
    // Create layout
    $layout = Zend_Layout::startMvc();

    // Set options
    $layout->setViewBasePath(APPLICATION_PATH . "/install/layouts", 'Install_Layout_View')
      ->setViewSuffix('tpl')
      ->setLayout('default');

    return $layout;
  }

  protected function _initView()
  {
    $view = new Zend_View();
    $view->setEncoding('utf-8');

    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setView($view);
    $viewRenderer->setViewSuffix('tpl');

    $view->addHelperPath(APPLICATION_PATH . '/application/libraries/Engine/View/Helper', 'Engine_View_Helper');
    $view->addHelperPath(APPLICATION_PATH . '/install/views/helpers', 'Install_View_Helper');

    Zend_Registry::set('Zend_View', $view);
    
    return $view;
  }

  protected function _initTranslate()
  {
    // If in development, log untranslated messages
    $params = array(
      'scan' => Zend_Translate_Adapter::LOCALE_FILENAME,
      'logUntranslated' => true
    );

    $log = new Zend_Log();
    if( APPLICATION_ENV == 'development' ) {
      $log = new Zend_Log();
      $log->addWriter(new Zend_Log_Writer_Firebug());
    } else {
      $log->addWriter(new Zend_Log_Writer_Null());
    }
    $params['log'] = $log;

    $translate = new Zend_Translate(
      'Csv',
      APPLICATION_PATH.'/install/languages',
      null,
      $params
    );

    Zend_Registry::set('Zend_Translate', $translate);
    Zend_Validate_Abstract::setDefaultTranslator($translate);
    Engine_Sanity::setDefaultTranslator($translate);

    return $translate;
  }

  protected function _initDb()
  {
    if( file_exists(APPLICATION_PATH . '/application/settings/database.php') ) {
      $config = include APPLICATION_PATH . '/application/settings/database.php';
      if( !empty($config['adapter']) ) {
        try {
          $db = Zend_Db::factory($config['adapter'], $config['params']);
          $db->getServerVersion(); // Forces the connection open
        } catch( Exception $e ) {
          $this->getContainer()->log->log($e, Zend_Log::WARN);
          return;
        }
        Zend_Registry::set('Zend_Db', $db);

        // set DB to UTC timezone for this session
        switch ($config['adapter']) {
          case 'mysqli':
          case 'mysql':
          case 'pdo_mysql': {
              $db->query("SET time_zone = '+0:00'");
              break;
          }

          case 'postgresql': {
              $db->query("SET time_zone = '+0:00'");
              break;
          }

          default: {
            // do nothing
          }
        }

        // attempt to disable strict mode
        try {
          $db->query("SET SQL_MODE = ''");
        } catch (Exception $e) {}

        Engine_Db_Table::setDefaultAdapter($db);

        return $db;
      }
    }
  }

  protected function _initAuth()
  {
    // Check if installed
    $installed = false;
    if( file_exists(APPLICATION_PATH . '/application/settings/database.php') ) {
      $installed = true;
    }
    Zend_Registry::set('Engine/Installed', $installed);

    // Check auth
    $auth = Zend_Auth::getInstance();

    if( defined('_ENGINE_NO_AUTH') && _ENGINE_NO_AUTH ) {
      $auth->getStorage()->write(1);
    }

    Zend_Registry::set('Zend_Auth', $auth);
    
    return $auth;
  }

  protected function _initPackageManager()
  {
    if( $this->getContainer()->auth->hasIdentity() ) {
      $packageManager = new Engine_Package_Manager(array(
        'basePath' => APPLICATION_PATH,
      ));
      Zend_Registry::set('Engine_Package_Manager', $packageManager);
      return $packageManager;
    }
  }

  protected function _initNavigationMain()
  {
    $navigation = new Zend_Navigation(array(
      array(
        'label' => 'Manage Packages',
        'route' => 'manage',
      ),
      array(
        'label' => 'Developer SDK',
        //'route' => 'sdk',
        'route' => 'default',
        'controller' => 'sdk',
      ),
      array(
        'label' => 'Import Tools',
        //'route' => 'import',
        'route' => 'default',
        'controller' => 'import',
      ),
      array(
        'label' => 'Other Tools',
        //'route' => 'tools',
        'route' => 'default',
        'controller' => 'tools',
      ),
      array(
        'label' => 'Logout',
        'route' => 'logout',
      ),
    ));

    Zend_Registry::set('Install_Navigation_Main', $navigation);
    $layout = $this->getContainer()->layout;
    //$layout = new Zend_Layout();
    $layout->mainNavigation = $navigation;

    return $navigation;
  }
}