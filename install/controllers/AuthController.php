<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AuthController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class AuthController extends Zend_Controller_Action
{
  public function init()
  {
    // Login has a layout param to prevent "branding"
    $this->view->layout()->hideIdentifiers = true;

    // Add install socialengine title
    $this->view->headTitle()->prepend('Install');
  }
  
  public function loginAction()
  {
    // Check if already logged in
    if( Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    $this->view->form = $form = new Install_Form_Auth();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $values = $form->getValues();

    // Try db first
    $error = null;
    try {
      $result = $this->_authDb($values['email'], $values['password']);
    } catch( Exception $e ) {
      $error = $e->getMessage();
    }

    if( !$error ) {
      if( $result ) {
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      } else {
        //return $form->addError('Invalid credentials');
      }
    }

    // Now try digest
    $prevError = $error;
    $error = null;
    try {
      $result = $this->_authDigest($values['email'], $values['password']);
    } catch( Exception $e ) {
      $error = $e->getMessage();
    }

    if( !$error ) {
      if( $result ) {
        return $this->_helper->redirector->gotoRoute(array(), 'default', true);
      } else {
        //return $form->addError('Invalid credentials');
      }
    }

    return $form->addError('Invalid credentials');

    //$form->addError('Unable to authenticate: no database or auth file found');
    //if( $error ) $form->addError($error);
    //if( $prevError ) $form->addError($prevError);
  }

  public function logoutAction()
  {
    $auth = Zend_Registry::get('Zend_Auth');
    $auth->clearIdentity();

    $return = $this->_getParam('return');
    if( !$return ) {
      // @todo change this to admin panel?
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    } else {
      return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
    }
  }

  public function keyAction()
  {
    // Check params
    $key = $this->_getParam('key');
    $user_id = $this->_getParam('uid');
    $return = $this->_getParam('return');

    if( strlen((string) $key) != 40 || !is_numeric($user_id) ) {
      $this->view->status = false;
      $this->view->error = 'Invalid key or user id provided';
      return;
    }

    // Try to get db
    if( !Zend_Registry::isRegistered('Zend_Db') || !(($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract) ) {
      $this->view->status = false;
      $this->view->error = 'Database connection not available, please login manually.';
      return;
    }
    
    // Try to pull from auth table
    $table = new Zend_Db_Table(array(
      'name' => 'engine4_core_auth',
    ));

    $row = $table->fetchRow(array(
      'id = ?' => $key,
      'user_id = ?' => $user_id,
      'type = ?' => 'package',
      '(expires = 0 || expires > ?)' => time(),
    ));

    if( null === $row ) {
      $this->view->status = false;
      $this->view->error = 'Unable to authenticate. Key is invalid or has expired';
      return;
    }
    
    // Everything checked out

    // Write auth (only if not already written)
    $auth = Zend_Registry::get('Zend_Auth');
    if( !$auth->getStorage()->read() ) {
      $auth->getStorage()->write($row->user_id);
    }
    
    // Delete row
    $row->delete();

    // Redirect
    if( null === $return ) {
      return $this->_helper->redirector->gotoRoute(array(), 'manage', true);
    } else {
      return $this->_helper->redirector->gotoUrl($return);
    }
  }





  protected function _authDb($identity, $credential)
  {
    $auth = Zend_Registry::get('Zend_Auth');

    // Check if it's possible to authenticate
    if( !Zend_Registry::isRegistered('Zend_Db') || !(($db = Zend_Registry::get('Zend_Db')) instanceof Zend_Db_Adapter_Abstract) ) {
      throw new Engine_Exception('Unable to authenticate, no database connection present');
    }
    
    // Make user table and level table
    try {
      $userTable = new Zend_Db_Table(array(
        'db' => $db,
        'name' => 'engine4_users',
      ));
      $userTable->info(); // Forces check on table existence
      $levelTable = new Zend_Db_Table(array(
        'db' => $db,
        'name' => 'engine4_authorization_levels',
      ));
      $levelTable->info(); // Forces check on table existence
      $settingsTable = new Zend_Db_Table(array(
        'db' => $db,
        'name' => 'engine4_core_settings',
      ));
      $settingsTable->info(); // Forces check on table existence
    } catch( Exception $e ) {
      throw new Engine_Exception('Unable to authenticate, missing database tables');
    }

    // Try to authenticate
    try {

      // Get static salt
      $staticSalt = $settingsTable->find('core.secret')->current();
      if( is_object($staticSalt) ) {
        $staticSalt = $staticSalt->value;
      } else {
        $staticSalt = '';
      }

      // Get superadmin levels
      $saLevels = $levelTable->select()->where('flag = ?', 'superadmin')->query()->fetchAll();
      $saLevelIds = array();
      foreach( (array) $saLevels as $dat ) {
        if( is_numeric($dat['level_id']) ) {
          $saLevelIds[] = $dat['level_id'];
        }
      }
      if( empty($saLevelIds) ) {
        return $form->addError('No admin levels');
      }
      $saLevelStr = "'" . join("','", $saLevelIds) . "'";
      
      // Authenticate
      $authAdapter = new Zend_Auth_Adapter_DbTable(
        $db,
        'engine4_users',
        'email',
        'password',
        "MD5(CONCAT('".$staticSalt."', ?, salt)) && `level_id` IN({$saLevelStr})"
      );

      $authAdapter
        ->setIdentity($identity)
        ->setCredential($credential);

      $authResult = $auth->authenticate($authAdapter);

    } catch( Exception $e ) {
      throw new Engine_Exception('An error occurred');
    }

    // Check result
    $authCode = $authResult->getCode();
    if( $authCode != Zend_Auth_Result::SUCCESS )
    {
      return false;
    }

    return true;
  }

  protected function _authDigest($identity, $credential)
  {
    defined('SEIRAN_INSTALL') || define('SEIRAN_INSTALL', true);

    $auth = Zend_Registry::get('Zend_Auth');
    
    if( !file_exists(APPLICATION_PATH . '/install/config/auth.php') ) {
      throw new Engine_Exception('Unable to authenticate, no auth file found.');
    }

    try {

      // May need to use this in some cases
      //if( !preg_match('/^[0-9a-f]{32}$/i', $credential) ) {
      //  $credential = md5($identity . ':' . 'sei-ran' . ':' . $credential);
      //}
      $authAdapter = new Zend_Auth_Adapter_Digest(APPLICATION_PATH . '/install/config/auth.php',
        'seiran', $identity, $credential);

      $authResult = $auth->authenticate($authAdapter);
      //var_dump($authResult);

    } catch( Exception $e ) {
      throw new Engine_Exception('An error occurred');
    }

    // Check result
    $authCode = $authResult->getCode();
    if( $authCode != Zend_Auth_Result::SUCCESS )
    {
      return false;
    }

    return true;
  }
}