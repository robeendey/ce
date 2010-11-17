<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: SettingsController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class SettingsController extends Zend_Controller_Action
{
  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Check if related folders are writeable
    if( !is_writeable(APPLICATION_PATH . '/install/config') ) {
      throw new Engine_Exception('install/config folder is not writeable');
    }
    
    // Add manage socialengine title
    $this->view->headTitle()->prepend('Manage SocialEngine');

    // Create navigation
    $this->view->navigation = new Zend_Navigation(array(
      array(
        'label' => 'Manage Packages',
        'route' => 'manage',
      ),
      array(
        'label' => 'Install Packages',
        'route' => 'manage',
        'action' => 'select',
      ),
      array(
        'label' => 'Logout',
        'route' => 'logout',
      ),
    ));
  }

  public function indexAction()
  {
    $filename = APPLICATION_PATH . '/install/config/general.php';
    $this->view->form = $form = new Install_Form_Settings_General();

    if( !$this->getRequest()->isPost() ) {
      ob_start();
      $config = include $filename;
      ob_end_clean();

      if( is_array($config) ) {
        $form->populate($config);
      }
      return;
    }
    
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    if( (file_exists($filename) && !is_writable($filename)) || (!file_exists($filename) && !is_writable(dirname($filename))) ) {
      $form->addError('Config file ' . $filename . ' is not writable');
      return;
    }

    file_put_contents($filename, '<?php return ' . var_export($form->getValues(), true) . '; ?>');
  }
}