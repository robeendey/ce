<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BackupController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class BackupController extends Zend_Controller_Action implements Engine_Observer_Interface
{
  protected $_outputPath;
  
  protected $_export;

  /**
   * @var Engine_Vfs_Adapter_Abstract
   */
  protected $_vfs;

  protected $_vfsSession;

  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $this->_outputPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary'
      . DIRECTORY_SEPARATOR . 'backup';

    if( !is_dir($this->_outputPath) || !is_writable($this->_outputPath) ) {
      throw new Engine_Exception(sprintf('Backup path "%s" is not writable or does not exist.', $this->_outputPath));
    }

    // Init session
    $this->_session = new Zend_Session_Namespace('Engine_Installer_Vfs');

    // Init vfs
    if( isset($this->_session->instance) && $this->_session->instance instanceof Engine_Vfs_Adapter_Abstract ) {
      $this->_vfs = $this->_session->instance;
    }
  }
  
  public function indexAction()
  {
    $backups = array();
    $it = new DirectoryIterator($this->_outputPath);
    foreach( $it as $file ) {
      if( !$file->isFile() ) continue;
      $pathname = $file->getPathname();
      if( strtolower(substr($pathname, -4)) !== '.tar' &&
          strtolower(substr($pathname, -4)) !== '.zip' &&
          strtolower(substr($pathname, -7)) !== '.tar.gz' ) continue;
      $backups[] = $pathname;
    }
    $this->view->backups = $backups;
  }

  public function createAction()
  {
    // Require
    require_once 'PEAR.php';
    require_once 'Archive/Tar.php';

    // Form
    $this->view->form = $form = new Install_Form_Backup_Create();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    set_time_limit(0);
    $values = $form->getValues();

    // Make filename
    $archiveFileName = $values['name'];
    $archiveFileName = preg_replace('/[^a-zA-Z0-9_.-]/', '', $archiveFileName);
    if( strtolower(substr($archiveFileName, -4)) != '.tar' ) {
      $archiveFileName .= '.tar';
    }
    $archiveFileName = $this->_outputPath . DIRECTORY_SEPARATOR . $archiveFileName;

    // setup paths
    $archiveSourcePath = APPLICATION_PATH;
    $tmpPath = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

    // Make archive
    $archive = new Archive_Tar($archiveFileName);
    
    // Add files
    $path = $archiveSourcePath;
    $files = array();
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
    foreach( $it as $file ) {
      $pathname = $file->getPathname();
      if( $file->isFile() ) {
        if( substr($pathname, 0, strlen($tmpPath)) == $tmpPath ) {
          continue;
        } else {
          $files[] = $pathname;
        }
      }
    }
    $ret = $archive->addModify($files, '', $path);
    if( PEAR::isError($ret) ) {
      throw new Engine_Exception($ret->getMessage());
    }
    
    // Add temporary structure only
    /*
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpPath), RecursiveIteratorIterator::SELF_FIRST);
    foreach( $it as $file ) {
      if( $file->isFile() ) {
        continue;
      } else {
        $path = str_replace(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR, '', $file->getPathname());
        $path .= DIRECTORY_SEPARATOR . 'index.html';
        $archive->addString($path, '');
      }
    }
     * 
     */
    
    // Export database
    $dbTempFile = $this->_createTemporaryFile();
    $db = Zend_Registry::get('Zend_Db');
    $export = Engine_Db_Export::factory($db, array(
      //'listeners' => array($this),
    ));
    $this->_export = $export;
    $export->write($dbTempFile);

    $archive->addString('database.sql', file_get_contents($dbTempFile));

    unlink($dbTempFile);

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function downloadAction()
  {
    $backup = $this->_getParam('backup');

    $archiveFilename = $this->_outputPath . DIRECTORY_SEPARATOR . $backup;
    if( '' == $backup || !is_file($archiveFilename) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Prepare
    set_time_limit(0);
    $size = filesize($archiveFilename);

    // Close output buffering
    while( ob_get_level() > 0 ) {
      ob_end_clean();
    }

    // Send headers
    header('content-type: application/x-tar');
    header('content-disposition: attachment, filename=' . urlencode(basename($archiveFilename)));
    header('content-length: ' . $size);

    $fh = fopen($archiveFilename, 'r');
    //$len = 0;
    while( !feof($fh) /*$size > $len*/ ) {
      $str = fread($fh, 8192);
      //$len += strlen($str);
      echo $str;
    }

    exit();
  }

  public function restoreAction()
  {
    // Require
    require_once 'PEAR.php';
    require_once 'Archive/Tar.php';

    // Param
    $backup = $this->_getParam('backup');
    
    // Verify backup
    $archiveFilename = $this->_outputPath . DIRECTORY_SEPARATOR . $backup;
    if( '' == $backup || !is_file($archiveFilename) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Check for vfs instance
    if( !($this->_vfs instanceof Engine_Vfs_Adapter_Abstract) ) {
      $this->_session->return = $_SERVER['REQUEST_URI'];
      return $this->_helper->redirector->gotoRoute(array('controller' => 'vfs', 'action' => 'index'));
    }

    // Check for database instance
    if( !(($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract) ) {
      throw new Engine_Exception('No database instance');
    }

    // Confirm/Options
    $this->view->form = $form = new Install_Form_Backup_Restore();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // !!IMPORTANT!!
    set_time_limit(0);
    ignore_user_abort(true);

    // Errors
    $errors = array();

    // Make temporary folder
    $archiveOutputPath = substr($archiveFilename, 0, strrpos($archiveFilename, '.'));
    if( is_dir($archiveOutputPath) ) {
      Engine_Package_Utilities::fsRmdirRecursive($archiveOutputPath, true);
    }
    if( !mkdir($archiveOutputPath, 0777, true) ) {
      throw new Engine_Exception(sprintf('Unable to make path %s', $archiveOutputPath));
    }

    // Extract
    $archive = new Archive_Tar($archiveFilename);
    $archive->extract($archiveOutputPath);

    // Upload
    $path = APPLICATION_PATH;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
    foreach( $it as $file ) {
      $fullPath = $file->getPathname();
      $partialPath = ltrim(str_replace($path, '', $fullPath), '/\\');

      if( is_dir($fullPath) ) {
        try {
          $this->_vfs->makeDirectory($directory, true);
        } catch( Exception $e ) {
          $errors[] = $e->__toString();
        }
      } else {
        try {
          $this->_vfs->put($partialPath, $fullPath);
        } catch( Exception $e ) {
          $errors[] = $e->__toString();
        }
      }
    }

    // Database
    //$db = new Zend_Db_Adapter_Mysqli();
    $queries = Engine_Package_Utilities::sqlSplit(file_get_contents($archiveOutputPath . '/database.sql'));
    foreach( $queries as $query ) {
      try {
        $db->query($query);
      } catch( Exception $e ) {
        $errors[] = $e->__toString();
      }
    }

    var_dump($errors);

    die('DONE!');
  }

  public function deleteAction()
  {
    $backup = $this->_getParam('backup');

    $archiveFilename = $this->_outputPath . DIRECTORY_SEPARATOR . $backup;
    if( '' == $backup || !is_file($archiveFilename) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    @unlink($archiveFilename);

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  protected function _createTemporaryFile()
  {
    $file = tempnam('/tmp', 'en4_install_backup');
    if( !$file ) {
      throw new Engine_Exception('Unable to create temp file');
    }
    return $file;
  }

  public function notify($event)
  {
    
  }
}