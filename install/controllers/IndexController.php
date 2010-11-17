<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class IndexController extends Zend_Controller_Action
{
  public function indexAction()
  {
    // If we haven't installed yet
    if( !Zend_Registry::get('Engine/Installed') ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'license'), 'install', true);
    }
    
    // Get auth info
    $auth = Zend_Registry::get('Zend_Auth');
    $this->view->identity = $identity = $auth->getIdentity();

    // We have installed and are logged in
    if( $identity ) {
      return $this->_helper->redirector->gotoRoute(array(), 'manage', true);
    }

    // We have to login
    else {
      return $this->_helper->redirector->gotoRoute(array(), 'login', true);
    }
  }
}