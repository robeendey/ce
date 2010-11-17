<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 7284 2010-09-03 19:28:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Chat_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('chat_admin_main', array(), 'chat_admin_main_settings');
    
    $this->view->form = $form = new Chat_Form_Admin_Settings_Global();
    
    // Prepare data
    $settingsApi = Engine_Api::_()->getApi('settings', 'core');
    $previous = $settingsApi->getFlatSetting('chat');

    if( !$this->getRequest()->isPost() ) {
      $form->populate($previous);
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $reconfigure = array();
    foreach( $values as $key => $value ) {
      if( $value != $previous[$key] ) {
        $reconfigure[$key] = $value;
      }
    }

    $settingsApi->setSetting('chat', $values);

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Changes have been saved.'));

    // Reconfigure connected clients
    if( !empty($reconfigure) ) {
      $eventTable = Engine_Api::_()->getDbtable('events', 'chat');
      $onlineUsers = Engine_Api::_()->getDbtable('users', 'chat')->fetchAll();
      foreach( $onlineUsers as $onlineUser ) {
        $eventTable->insert(array(
          'type' => 'reconfigure',
          'user_id' => $onlineUser->user_id,
          'body' => $reconfigure,
          'date' => date('Y-m-d H:i:s'),
        ));
      }

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Reconfigure directive was issued to all connected members.'));
    }
  }

  public function levelAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('chat_admin_main', array(), 'chat_admin_main_level');

    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }

    if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $level_id = $id = $level->level_id;

    // Make form
    $this->view->form = $form = new Chat_Form_Admin_Settings_Level(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);

    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $previous = $permissionsTable->getAllowed('chat', $id, array_keys($form->getValues()));
    $form->populate($previous);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $values = $form->getValues();
    $reconfigure = array();
    if( $values['chat'] != $previous['chat'] ) {
      $reconfigure['chat_enabled'] = $values['chat'];
    }
    if( $values['im'] != $previous['im'] ) {
      $reconfigure['im_enabled'] = $values['im'];
    }

    $permissionsTable->setAllowed('chat', $id, $values);

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Changes have been saved.'));

    // Reconfigure connected clients
    if( !empty($reconfigure) ) {
      $eventTable = Engine_Api::_()->getDbtable('events', 'chat');
      $onlineUsers = Engine_Api::_()->getDbtable('users', 'chat')->fetchAll();
      foreach( $onlineUsers as $onlineUser ) {
        $user = Engine_Api::_()->getItem('user', $onlineUser->user_id);
        if( !$user || $user->level_id != $id ) continue;
        $eventTable->insert(array(
          'type' => 'reconfigure',
          'user_id' => $onlineUser->user_id,
          'body' => $reconfigure,
          'date' => date('Y-m-d H:i:s'),
        ));
      }

      $form->addNotice(Zend_Registry::get('Zend_Translate')->_("Reconfigure directive was issued to all connected members in this level."));
    }
  }
}