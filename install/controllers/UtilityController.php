<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: UtilityController.php 7539 2010-10-04 04:41:38Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class UtilityController extends Zend_Controller_Action
{
  protected $_session;
  
  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    
    // Session
    $this->_session = new Zend_Session_Namespace('InstallUtilityController');

    // Return
    if( false != ($return = $this->_getParam('return')) ) {
      $this->_session->return = $return;
    } else if( empty($this->_session->return) && !empty($_SERVER['HTTP_REFERER']) ) {
      $this->_session->return = $_SERVER['HTTP_REFERER'];
    }
  }

  public function dbAction()
  {
    $this->view->form = $form = new Install_Form_DbInfo();

    // Make session
    if( $this->_getParam('clear') ) {
      unset($this->_session->database);
    }

    if( !$this->getRequest()->isPost() ) {
      if( !empty($this->_session->database) && is_array($this->_session->database) ) {
        $vals = $this->_session->database['params'];
        unset($vals['password']);
        $form->populate($vals);
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $params = $form->getValues();
    $adapter = $params['adapter'];
    unset($params['adapter']);

    // Add some special magic
    if( $adapter == 'mysqli' ) {
      $params['driver_options'] = array(
        'MYSQLI_OPT_CONNECT_TIMEOUT' => '2',
      );
    } else if( $adapter == 'pdo_mysql' ) {
      $params['driver_options'] = array(
        Zend_Db::ATTR_TIMEOUT => '2',
      );
    }

    // Validate mysql options
    try {
      // Connect!
      $adapterObject = Zend_Db::factory($adapter, $params);
      $adapterObject->getServerVersion();

    } catch( Exception $e ) {
      return $form->addError('Adapter Error: ' . $e->getMessage());
    }

    $this->_session->database['adapter'] = $adapter;
    $this->_session->database['params'] = $params;


    return $this->_doSuccessRedirect();
  }
  
  public function vfsAction()
  {
    // Get vfs config
    $vfsConfig = array();
    $vfsFile = APPLICATION_PATH . '/install/config/vfs.php';
    if( file_exists($vfsFile) ) {
      $vfsConfig = include $vfsFile;
    }
    $this->view->form = $form = new Install_Form_VfsInfo();

    // Get current vfs config
    $vfsConfigCurrent = $this->_session->vfs;
    if( !is_array($vfsConfigCurrent) ) {
      $vfsConfigCurrent = array();
    }

    // Adapter type
    $adapterType = $this->_getParam('adapter');
    if( null === $adapterType ) {
      if( !empty($vfsConfigCurrent['adapter']) ) {
        $adapterType = @$vfsConfigCurrent['adapter'];
      } else {
        $adapterType = @$vfsConfig['adapter'];
      }
    }
    $previousAdapterType = $this->_getParam('previousAdapter');
    
    // Form
    $this->view->form = $form = new Install_Form_VfsInfo(array(
      'adapterType' => $adapterType,
    ));

    // Populate
    if( !$this->getRequest()->isPost() || $adapterType != $previousAdapterType ) {
      if( !$adapterType ) {
        // Ignore
      } else if( $adapterType == @$vfsConfigCurrent['adapter'] ) {
        // Load from session
        $form->populate($vfsConfigCurrent);
      } else if( $adapterType == @$vfsConfig['adapter'] ) {
        // Load from settings file
        $form->populate($vfsConfig);
      } else {
        $form->populate(array(
          'adapter' => $adapterType,
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $vfsConfigCurrent = $form->getValues();

    // Try to load adapter
    try {
      $vfs = Engine_Vfs::factory($vfsConfigCurrent['adapter'], $vfsConfigCurrent['config']);
    } catch( Exception $e ) {
      $form->addError('Connection error: ' . $e->getMessage());
      return;
    }

    // Try to connect (getResource will force connect)
    try {
      $vfs->getResource();
    } catch( Exception $e ) {
      $form->addError('Connection error: ' . $e->getMessage());
      return;
    }

    // Search for target
    $path = null;
    if( !empty($vfsConfigCurrent['config']['search']) && $vfsConfigCurrent['config']['search'] !== '0' ) {
      $path = $vfs->findJailedPath($vfsConfig['path'], APPLICATION_PATH . '/install/ftp_search_will_look_for_this_file');
      if( !$path ) {
        $form->addError('Your installation could not be found. Please double check your connection info and starting path. Your starting path needs to be your SocialEngine path, or a parent directory of it.');
        return;
      }
      $path = dirname(dirname($path));
    } else {
      $path = $vfsConfigCurrent['config']['path'];
    }

    // Verify path
    if( !$vfs->exists($path . '/install/ftp_search_will_look_for_this_file') ) {
      $form->addError('Specified path is not a SocialEngine install directory.');
      return;
    }

    // Save config
    $vfsConfigCurrent = $vfsConfig = array(
      'adapter' => $vfsAdapter,
      'config' => $vfsConfig,
    );


    $vfsConfigCurrent['config']['path'] = $path;

    $vfs->changeDirectory($path);

    $this->_session->vfsInstance = $vfs;

    $this->_session->vfs = $vfsConfigCurrent;
    
    // Save for later
    $vfsSettingsData = array(
      'adapter' => $vfsAdapter,
      'config' => array_diff_key($vfsConfigCurrent, array(
        'password' => null,
        'location' => null,
        'search' => null,
      )),
    );
    @file_put_contents(APPLICATION_PATH . '/install/config/vfs.php', '<?php return ' . var_export($vfsSettingsData, true) . '?>');


    return $this->_doSuccessRedirect();
  }


  protected function _doSuccessRedirect()
  {
    if( !empty($this->_session->return) ) {
      $return = $this->_session->return;
      $this->_session->return = null;
    } else {
      $return = $this->view->url(array(), 'default', true); // Sigh
    }
    return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
  }
}