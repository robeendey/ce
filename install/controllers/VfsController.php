<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: VfsController.php 7539 2010-10-04 04:41:38Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class VfsController extends Zend_Controller_Action
{
  protected $_session;
  
  protected $_vfsSettings;
  
  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Init session
    $this->_session = new Zend_Session_Namespace('Engine_Installer_Vfs');

    // Get vfs config
    $vfsConfig = array();
    $vfsFile = APPLICATION_PATH . '/install/config/vfs.php';
    if( file_exists($vfsFile) ) {
      $vfsConfig = include $vfsFile;
    }
    $this->_vfsSettings = $vfsConfig;
  }

  public function indexAction()
  {
    $this->view->form = $form = new Install_Form_VfsInfo();

    // Adapter type
    $adapterType = $this->_getParam('adapter');
    if( null === $adapterType ) {
      $adapterType = $this->_session->vfsAdapter;
      if( null === $adapterType ) {
        $adapterType = @$this->_vfsSettings['adapter'];
      }
    }
    $previousAdapterType = $this->_getParam('previousAdapter');

    // Return
    $return = $this->_getParam('return');
    if( !$return ) {
      if( !empty($_SERVER['HTTP_REFERER']) && false != ($parts = parse_url($_SERVER['HTTP_REFERER'])) &&
          $parts['host'] == $_SERVER['HTTP_HOST'] ) {
        $return = $parts['path'] . ( !empty($parts['query']) ? '?' . $parts['query'] : '' );
      }
    }
    if( !$return ) {
      $return = $this->view->url(array('controller' => 'manage', 'action' => 'index'));
    }

    // Form
    $this->view->form = $form = new Install_Form_VfsInfo(array(
      'adapterType' => $adapterType,
    ));

    // Populate
    if( !$this->getRequest()->isPost() || $adapterType != $previousAdapterType ) {
      if( !$adapterType ) {
        // Ignore
      } else if( $adapterType == @$this->_session->vfsAdapter ) {
        // Load from session
        $form->populate(array(
          'adapter' => $adapterType,
          'config'  => $this->_session->vfsConfig,
          'return'  => $return,
        ));
      } else if( $adapterType == @$this->_vfsSettings['adapter'] ) {
        // Load from settings file
        $form->populate(array_merge($this->_vfsSettings, array(
          'return'  => $return,
        )));
      } else {
        $form->populate(array(
          'adapter' => $adapterType,
          'return'  => $return,
        ));
      }
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $vfsAdapter = $values['adapter'];
    $vfsConfig = $values['config'];

    // Try to load adapter
    try {
      $vfs = Engine_Vfs::factory($vfsAdapter, $vfsConfig);
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
    if( !empty($vfsConfig['search']) && $vfsConfig['search'] !== '0' ) {
      $path = $vfs->findJailedPath($vfsConfig['path'], APPLICATION_PATH . '/install/ftp_search_will_look_for_this_file');
      if( !$path ) {
        $form->addError('Your installation could not be found. Please double check your connection info and starting path. Your starting path needs to be your SocialEngine path, or a parent directory of it.');
        return;
      }
      $path = dirname(dirname($path));
    } else {
      $path = $vfsConfig['path'];
    }

    // Verify path
    if( !$vfs->exists($path . '/install/ftp_search_will_look_for_this_file') ) {
      $form->addError('Specified path is not a SocialEngine install directory.');
      return;
    }

    // Save config
    $vfsConfig['path'] = $values['config']['path'] = $path;

    $vfs->changeDirectory($path);
    $this->_session->instance = $vfs;

    $this->_session->adapter = $vfsAdapter;
    $this->_session->config = $vfsConfig;

    $this->_vfsSettings = $values;

    // Save for later
    $vfsSettingsData = array(
      'adapter' => $vfsAdapter,
      'config' => array_diff_key($vfsConfig, array(
        'password' => null,
        'location' => null,
        'search' => null,
      )),
    );
    @file_put_contents(APPLICATION_PATH . '/install/config/vfs.php', '<?php return ' . var_export($vfsSettingsData, true) . '?>');

    // Redirect
    if( !empty($this->_session->return) ) {
      $return = $this->_session->return;
      $this->_session->return = null;
    }
    return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
  }
}