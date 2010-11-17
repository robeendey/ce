<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: CompareController.php 7432 2010-09-20 23:30:18Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class ToolsController extends Zend_Controller_Action
{
  /**
   * @var Engine_Package_Manager
   */
  protected $_packageManager;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Zend_Cache_Core
   */
  protected $_cache;

  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Get manager
    $this->_packageManager = Zend_Registry::get('Engine_Package_Manager');

    // Get session
    $this->_session = new Zend_Session_Namespace('InstallToolsController');

    // Get cache
    if( !Zend_Registry::isRegistered('Cache') ) {
      throw new Engine_Exception('Cache could not be initialized. Please try setting full permissions on temporary/cache');
    }
    $this->_cache = Zend_Registry::get('Cache');

    // Get old path
    $this->_oldPath = APPLICATION_PATH . '/temporary/package/old';

    if( !is_dir($this->_oldPath) ) {
      if( !mkdir($this->_oldPath, 0777, true) ) {
        throw new Engine_Exception(sprintf('Temporary path %1$s does not exist and could not be created.', $this->_oldPath));
      }
    }
  }

  public function __call($method, $args = array())
  {
    // Proxy externals for
    if( 'externalsAction' == $method ) {
      $path = APPLICATION_PATH . '/application/libraries/Adminer';
      list($base, $static) = explode('externals', $_SERVER['REQUEST_URI']);
      $this->_outputFile($path . '/externals' . $static);
      exit();
    }
    
    parent::__call($methodName, $args);
  }

  public function indexAction()
  {
    $this->view->hasAdminer = file_exists(APPLICATION_PATH . '/application/libraries/Adminer');
  }




  public function adminerAction()
  {
    // Get config
    $path = APPLICATION_PATH . '/application/libraries/Adminer';
    $adminerPath = $path . '/adminer';
    $configFile = APPLICATION_PATH . '/install/config/adminer.php';

    $config = array();
    if( file_exists($configFile) ) {
      $config = include $configFile;
    }

    // Adminer missing
    if( !file_exists($adminerPath . '/index.php') ) {
      throw new Engine_Exception('Adminer is missing');
    }

    // Proxy static resources
    else if( '' != $this->_getParam('static') ) {
      list($base, $static) = explode('static', $_SERVER['REQUEST_URI']);
      $this->_outputFile($adminerPath . '/static' . $static);
      exit();
    }

    // Adminer main
    else {
      // Check request uri
      list($request_filename) = explode('?', $_SERVER['REQUEST_URI']);
      if( substr($request_filename, -1) != '/' && false === strpos($request_filename, 'adminer/adminer.php') ) {
        header('Location: ' . $_SERVER['REQUEST_URI'] . '/');
        exit();
      }

      // Kill output buffering?
      while( ob_get_level() > 0 ) {
        ob_end_clean();
      }

      // Change directory
      chdir($adminerPath);

      // Add autologin
      if( !empty($config['autologin']) &&
          $_SERVER['REQUEST_METHOD'] == 'GET' &&
          empty($_SESSION["usernames"]) ) {
        $db = Zend_Registry::get('Zend_Db');
        $dbConfig = $db->getConfig();
        $_POST['server'] = $dbConfig['host'];
        $_POST['username'] = $dbConfig['username'];
        $_POST['password'] = $dbConfig['password'];
      }
      
      // Globals in: adminer.inc.php
      global $VERSION, $connection;
      // Globals in: auth.inc.php
      global $connection, $adminer;
      // Globals in: connect.inc.php
      global $connection, $VERSION, $token, $error;
      // Globals in: design.inc.php
      global $LANG, $VERSION, $adminer, $connection;
      // Globals in: editing.inc.php
      global $structured_types, $unsigned, $inout, $enum_length, $connection, $types;
      // Globals in: export.inc.php
      global $connection;
      // Globals in: functions.inc.php
      global $connection, $error, $adminer, $types;
      // Globals in: lang.inc.php
      global $LANG, $translations, $langs;
      // Globals in: mysql.inc.php
      global $adminer, $connection, $on_actions;

      define('_ENGINE_ADMINER', true);
      
      include $adminerPath . '/index.php';
      
      exit();
    }
  }
  
  public function apcAction()
  {
    
  }
  
  public function compareAction()
  {
    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    // Get cached diffs
    if( isset($this->_session->diffs) ) {
      $diffs = $this->_session->diffs;
    } else {
      $this->_session->diffs = $diffs = new Engine_Cache_ArrayContainer(array(), $this->_cache);
    }

    // Flush diffs
    if( $this->_getParam('flush') ) {
      $diffs->clean();
      unset($diffs);
      unset($this->_session->diffs);
      return $this->_helper->redirector->gotoRoute(array('flush' => null));
    }

    // Check for skip identical
    $showAll = (bool) $this->_getParam('all', false);

    // Build diffs
    if( $diffs->count() <= 0 ) {
      foreach( $packages as $key => $package ) {
        $operation = new Engine_Package_Manager_Operation_Install($this->_packageManager, $package);
        $fileOperations = $operation->getFileOperations(!$showAll);
        $fileOperations = $fileOperations['operations'];

        $indexedOperations = array();
        // Re-index file operations
        do {
          // Get key/val and remove
          $path = key($fileOperations);
          $info = $fileOperations[$path];
          $code = $info['key'];
          unset($fileOperations[$path]);

          if( !$showAll ) {
            if( $code == 'identical' ) {
              continue;
            }
          }

          // Save to index
          $indexedOperations[$code][$path] = $info;

          // Clear
          unset($path);
          unset($info);
          unset($code);
        } while( count($fileOperations) > 0 );
        
        unset($operation);
        unset($fileOperations);

        // Save cache
        if( !empty($indexedOperations) ) {
          $diffs->offsetSet($package->getKey(), $indexedOperations);
        }
        unset($indexedOperations);
      }
    }
    $this->view->diffs = $diffs;
    
    // Get extracted packages
    $oldPackages = array();
    $it = new DirectoryIterator($this->_packageManager->getTemporaryPath(Engine_Package_Manager::PATH_PACKAGES));
    foreach( $it as $child ) {
      if( $it->isDot() || $it->isFile() || !$it->isDir() ) {
        continue;
      }
      $oldPackages[] = $child->getBasename();
    }
    
    $this->view->oldPackages = $oldPackages;
  }

  public function conflictAction()
  {
    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    $index = array();
    $conflicts = array();

    foreach( $packages as $package ) {
      foreach( $package->getFileStructure() as $file ) {
        if( !isset($index[$file]) ) {
          $index[$file] = $package->getKey();
        } else {
          $conflicts[$file][] = $index[$file];
          $conflicts[$file][] = $package->getKey();
        }
      }
    }
    $this->view->conflicts = $conflicts;
  }

  public function diffAction()
  {
    if( $this->_getParam('hideIdentifiers') ) {
      $this->view->layout()->hideIdentifiers = true;
    }
    
    $left = $this->_getParam('left');
    $right = $this->_getParam('right');
    $file = $this->_getParam('file');
    $packageKey = $this->_getParam('package');
    $type = $this->_getParam('type', 'inline');
    $show = $this->_getParam('show', 0);

    // Left/right mode
    if( $left && $right ) {
      // Calculate base file?
      $file = '';
      for( $il = strlen($left) - 1, $ir = strlen($right) - 1; $il >= 0 && $ir >= 0; $il--, $ir-- ) {
        if( $left[$il] === $right[$ir] ) {
          $file = $left[$il] . $file;
        } else {
          break;
        }
      }
      $file = trim($file, '/\\');
      // Add base path
      if( $left[0] != '/' && $left[0] != '\\' ) {
        $left = APPLICATION_PATH . DIRECTORY_SEPARATOR . $left;
      }
      if( $right[0] != '/' && $right[0] != '\\' ) {
        $right = APPLICATION_PATH . DIRECTORY_SEPARATOR . $right;
      }
      // Make sure it's within the installation
      if( 0 !== strpos($left, APPLICATION_PATH) || 0 !== strpos($right, APPLICATION_PATH) ) {
        throw new Engine_Exception('Not within the installation');
      }
    }

    // File/Package mode
    else if( $file && $packageKey ) {
      $package = $this->_packageManager->listExtractedPackages()->offsetGet($packageKey);
      if( !$package ) {
        throw new Engine_Exception('Package does not exist.');
      }
      $left = $package->getBasePath() . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
      $right = APPLICATION_PATH . DIRECTORY_SEPARATOR . ltrim($file, '/\\');
    }

    // Whoops
    else {
      return;
    }

    // Must have at least left or right
    if( !$left && !$right ) {
      return;
    } else if( !file_exists($left) && !file_exists($right) ) {
      return;
    }

    // Assign
    $this->view->file = $file;
    $this->view->left = $left;
    $this->view->right = $right;

    // Options
    $this->view->type = $type;
    $this->view->showEverything = $show;
    $arr = array();
    parse_str($_SERVER['QUERY_STRING'], $arr);
    $this->view->parts = $arr;

    // Diff
    include_once 'Text/Diff.php';
    include_once 'Text/Diff/Renderer.php';
    include_once 'Text/Diff/Renderer/context.php';
    include_once 'Text/Diff/Renderer/inline.php';
    include_once 'Text/Diff/Renderer/unified.php';
    
    $this->view->textDiff = $textDiff = new Text_Diff(
      'native',//'auto',
      array(
        file_exists($left)  ? file($left,  FILE_IGNORE_NEW_LINES) : array(),
        file_exists($right) ? file($right, FILE_IGNORE_NEW_LINES) : array(),
      )
    );
  }

  public function phpAction()
  {
    ob_start();
    phpinfo();
    $source = ob_get_clean();

    preg_match('~<style.+?>(.+?)</style>.+?(<table.+\/table>)~ims', $source, $matches);
    $css = $matches[1];
    $source = $matches[2];

    $css = preg_replace('/[\r\n](.+?{)/iu', "\n#phpinfo \$1", $css);

    //$regex = '/'.preg_quote('<a href="http://www.php.net/">', '/').'.+?'.preg_quote('</a>', '/').'/ims';
    //$source = preg_replace($regex, '', $source);

    // strip images from phpinfo()
    $regex = '/<img .+?>/ims';
    $source = preg_replace($regex, '', $source);

    $regex = '/'.preg_quote('<h2>PHP License</h2>', '/').'.+$/ims';
    $source = preg_replace($regex, '', $source);

    $source = str_replace("module_Zend Optimizer", "module_Zend_Optimizer", $source);

    $this->view->style = $css;
    $this->view->content = $source;
  }

  public function sanityAction()
  {
    // Get db
    if( Zend_Registry::isRegistered('Zend_Db') && ($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract ) {
      Engine_Sanity::setDefaultDbAdapter($db);
    }

    // Get packages
    $packages = $this->_packageManager->listInstalledPackages();

    // Get dependencies
    $this->view->dependencies = $dependencies = $this->_packageManager->depend();

    // Get tests
    $this->view->tests = $tests = new Engine_Sanity();

    $packageIndex = array();
    foreach( $packages as $package ) {
      $packageTests = $package->getTests();

      // No tests
      if( empty($packageTests) ) {
        continue;
      }

      $packageIndex[$package->getKey()] = $package;

      // Make battery
      $battery = new Engine_Sanity(array(
        'name' => $package->getKey(),
      ));
      foreach( $packageTests as $test ) {
        $battery->addTest($test->toArray());
      }

      $tests->addTest($battery);
    }

    $this->view->packageIndex = $packageIndex;

    $tests->run();
  }

  protected function _outputFile($file, $exit = true)
  {
    $ext = trim(substr($file, strrpos($file, '.')), '.');
    switch( $ext ) {
      case 'css':
        header('Content-Type: text/css');
        break;
      case 'js':
        header('Content-Type: text/javascript');
        break;
      case 'jpg': case 'jpeg':
        header('Content-Type: image/jpeg');
        break;
      case 'png':
        header('Content-Type: image/png');
        break;
      case 'gif':
        header('Content-Type: image/gif');
        break;
      default:
        header('Content-Type: text/html');
        break;
    }
    echo file_get_contents($file);
    if( $exit ) {
      exit();
    }
  }
}