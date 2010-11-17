<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: SettingsController.php 7537 2010-10-04 01:10:44Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_SettingsController extends Core_Controller_Action_User
{
  protected $_user;

  public function init()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id )
    {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    }
    else
    {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );
    
    // Set up navigation
    $this->view->navigation = $navigation = $this->_helper->api()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
    
    $contextSwitch = $this->_helper->contextSwitch;
    $contextSwitch
      //->addActionContext('reject', 'json')
      ->initContext();
  }

  public function generalAction()
  {
    // Config vars
    $user = $this->_helper->api()->core()->getSubject();
    $this->view->form = $form = new User_Form_Settings_General(array('item' => $user));

    // Set up profile type options
    /*
    $aliasedFields = $user->fields()->getFieldsObjectsByAlias();
    if( isset($aliasedFields['profile_type']) )
    {
      $options = $aliasedFields['profile_type']->getElementParams($user);
      unset($options['options']['order']);
      $form->accountType->setOptions($options['options']);
    }
    else
    { */
      $form->removeElement('accountType');
    /* } */
    
    // Removed disabled features
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'username') ) {
      $form->removeElement('username');
    }

    // Facebook
    if ('none' != Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.enable', 'none')) {
      $facebook = User_Model_DbTable_Facebook::getFBInstance();
      if ($facebook->getSession()) {
        $fb_uid = Engine_Api::_()->getDbtable('facebook', 'user')->fetchRow(array('user_id = ?'=>Engine_Api::_()->user()->getViewer()->getIdentity()));
        if ($fb_uid && $fb_uid->facebook_uid)
            $fb_uid  = $fb_uid->facebook_uid;
        else
            $fb_uid  = null;

        try {
          $facebook->api('/me');
          if ($fb_uid && $facebook->getUser() != $fb_uid) {
            $form->removeElement('facebook_id');
            $form->getElement('facebook')->addError('You appear to be logged into a different Facebook account than what was registered with this account.  Please log out of Facebook using the button below to log into your correct Facebook account.');
            $form->getElement('facebook')->setContent($this->view->translate('<button onclick="window.location.href=this.value;return false;" value="%s">Logout of Facebook</button>', $facebook->getLogoutUrl()));
          } else {
            $form->removeElement('facebook');
            $form->getElement('facebook_id')->setAttrib('checked', (bool) $fb_uid);
          }
        } catch (Exception $e) {
          $form->removeElement('facebook');
          $form->removeElement('facebook_id');
        }
      } else {
        @$form->removeElement('facebook_id');
      }
    } else {
      // these should already be removed inside the form, but lets do it again.
      @$form->removeElement('facebook');
      @$form->removeElement('facebook_id');
    }


    // Check if post and populate
    if( !$this->getRequest()->isPost() )
    {
      $form->populate($user->toArray());
      
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid method');
      return;
    }

    // Check if valid
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // -- Process --

    // Set values for user object
    $user->setFromArray($form->getValues());
    $user->save();
    
    // Update account type
    /*
    $accountType = $form->getValue('accountType');
    if( isset($aliasedFields['profile_type']) )
    {
      $valueRow = $aliasedFields['profile_type']->getValue($user);
      if( null === $valueRow ) {
        $valueRow = Engine_Api::_()->fields()->getTable('user', 'values')->createRow();
        $valueRow->field_id = $aliasedFields['profile_type']->field_id;
        $valueRow->item_id = $user->getIdentity();
      }
      $valueRow->value = $accountType;
      $valueRow->save();
    }
     *
     */

    // Update facebook settings
    if (isset($facebook) && $form->getElement('facebook_id')) {
      if ($facebook->getSession()) {
        try {
          $facebook->api('/me');
          $uid   = Engine_Api::_()->user()->getViewer()->getIdentity();
          $table = Engine_Api::_()->getDbtable('facebook', 'user');
          $row   = $table->find($uid)->current();
          if (!$row) {
            $row = $table->createRow();
            $row->user_id = $uid;
          }
          $row->facebook_uid = $this->getRequest()->getPost('facebook_id')
                             ? $facebook->getUser()
                             : 0;
          $row->save();
          $form->removeElement('facebook');
        } catch (Exception $e) {}
      }
    }
    
    // Send success message
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Settings saved.');
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
  }

  public function privacyAction()
  {
    $user = $this->_helper->api()->core()->getSubject();
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $auth = Engine_Api::_()->authorization()->context;

    $this->view->form = $form = new User_Form_Settings_Privacy(array(
      'item' => $user,
    ));

    // Init blocked
    $this->view->blockedUsers = array();

    if( Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      foreach ($user->getBlockedUsers() as $blocked_user_id) {
        $this->view->blockedUsers[] = Engine_Api::_()->user()->getUser($blocked_user_id);
      }
    } else {
      $form->removeElement('blockList');
    }

    if( !Engine_Api::_()->getDbtable('permissions', 'authorization')->isAllowed($user, $user, 'search') ) {
      $form->removeElement('search');
    }

    if( count($form->privacy->options) <= 1 ) {
      $form->removeElement('privacy');
    }

    // Populate form
    $form->populate($user->toArray());

    // Set up activity options
    if( $form->getElement('publishTypes') ) {
      $actionTypes = Engine_Api::_()->getDbtable('actionTypes', 'activity')->getEnabledActionTypesAssoc();
      $form->publishTypes->setMultiOptions($actionTypes);
      $actionTypesEnabled = Engine_Api::_()->getDbtable('actionSettings', 'activity')->getEnabledActions($user);
      $form->publishTypes->setValue($actionTypesEnabled);
    }
    
    // Check if post and populate
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $form->save();
    $user->setFromArray($form->getValues())
      ->save();

    // Update notification settings
    if( $form->getElement('publishTypes') ) {
      $publishTypes = $form->publishTypes->getValue();
      Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array) $publishTypes);
    }
  }

  public function passwordAction()
  {
    $user = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new User_Form_Settings_Password();
    $form->populate($user->toArray());

    if( !$this->getRequest()->isPost() ){
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Check conf
    if( $form->getValue('passwordConfirm') !== $form->getValue('password') ) {
      $form->getElement('passwordConfirm')->addError(Zend_Registry::get('Zend_Translate')->_('Passwords did not match'));
      return;
    }
    
    // Process form
    $userTable = Engine_Api::_()->getItemTable('user');
    $db = $userTable->getAdapter();

    // Check old password
    $salt = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt');
    $select = $userTable->select()
      ->from($userTable, new Zend_Db_Expr('TRUE'))
      ->where('user_id = ?', $user->getIdentity())
      ->where('password = ?', new Zend_Db_Expr(sprintf('MD5(CONCAT(%s, %s, salt))', $db->quote($salt), $db->quote($form->getValue('oldPassword')))))
      ->limit(1)
      ;
    $valid = $select
      ->query()
      ->fetchColumn()
      ;

    if( !$valid ) {
      $form->getElement('oldPassword')->addError(Zend_Registry::get('Zend_Translate')->_('Old password did not match'));
      return;
    }

    
    // Save
    $db->beginTransaction();

    try {

      $user->setFromArray($form->getValues());
      $user->save();
      
      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Settings were successfully saved.'));
  }

  public function networkAction()
  {
    $this->view->navigation = $navigation = $this->_helper->api()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings');

    $viewer = $this->_helper->api()->user()->getViewer();

    $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($viewer)
      ->where('hide = ?', 0);
    $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

    $this->view->form = $form = new User_Form_Settings_Network();

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $form->getValue('join_id') ) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('join_id'));
      if( null === $network ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if( $network->assignment != 0 ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->addMember($viewer)
          ->setUserApproved($viewer)
          ->setResourceApproved($viewer);
        // Activity feed item
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $network, 'network_join');
      }
    } else if( $form->getValue('leave_id') ) {
      $network = Engine_Api::_()->getItem('network', $form->getValue('leave_id'));
      if( null === $network ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else if( $network->assignment != 0 ) {
        $form->addError(Zend_Registry::get('Zend_Translate')->_('Network not found'));
      } else {
        $network->membership()->removeMember($viewer);
      }
    }

    $this->_helper->redirector->gotoRoute(array());
  }

  public function notificationsAction()
  {
    $user = $this->_helper->api()->core()->getSubject();
    
    // Build the different notification types
    $modules = Engine_Api::_()->getDbtable('modules', 'core')->getModulesAssoc();
    $notificationTypes = Engine_Api::_()->getDbtable('notificationTypes', 'activity')->getNotificationTypes();
    $notificationSettings = Engine_Api::_()->getDbtable('notificationSettings', 'activity')->getEnabledNotifications($user);

    $notificationTypesAssoc = array();
    $notificationSettingsAssoc = array();
    foreach( $notificationTypes as $type ) {
      if( in_array($type->module, array('core', 'activity', 'fields', 'authorization', 'messages', 'user')) ) {
        $category = 'General';
      } else if( isset($modules[$type->module]) ) {
        $category = $modules[$type->module]->title;
      } else {
        $category = 'Misc';
      }

      $notificationTypesAssoc[$category][$type->type] = 'ACTIVITY_TYPE_' . strtoupper($type->type);

      if( in_array($type->type, $notificationSettings) ) {
        $notificationSettingsAssoc[$category][] = $type->type;
      }
    }

    ksort($notificationTypesAssoc);

    $notificationTypesAssoc = array_filter(array_merge(array(
      'General' => array(),
      'Misc' => array(),
    ), $notificationTypesAssoc));

    // Make form
    $this->view->form = $form = new Engine_Form(array(
      'title' => 'Notification Settings',
      'description' => 'Which of the these do you want to receive email alerts about?',
    ));

    foreach( $notificationTypesAssoc as $category => $categoryTypes ) {
      $form->addElement('MultiCheckbox', $category, array(
        'label' => $category,
        'multiOptions' => $categoryTypes,
        'value' => (array) @$notificationSettingsAssoc[$category],
      ));
    }

    $form->addElement('Button', 'execute', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));

    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    
    // Process
    $values = array();
    foreach( $form->getValues() as $key => $value ) {
      if( !is_array($value) ) continue;

      foreach( $value as $skey => $svalue ) {
        if( !isset($notificationTypesAssoc[$key][$svalue]) ) {
          continue;
        }
        $values[] = $svalue;
      }
    }
    
    // Set notification setting
    Engine_Api::_()->getDbtable('notificationSettings', 'activity')->setEnabledNotifications($user, $values);

    $form->addNotice('Your changes have been saved.');
  }

  public function deleteAction()
  {
    $user = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($user, null, 'delete')->isValid() ) return;

    $this->view->isLastSuperAdmin   = false;
    if( 1 === count(Engine_Api::_()->user()->getSuperAdmins()) && 1 === $user->level_id ) {
      $this->view->isLastSuperAdmin = true;
    }

    // Form
    $this->view->form = $form = new User_Form_Settings_Delete();

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('users', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $user->delete();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    // Unset viewer, remove auth, clear session
    $this->_helper->api()->user()->setViewer(null);
    Zend_Auth::getInstance()->getStorage()->clear();
    Zend_Session::destroy();

    return $this->_helper->redirector->gotoRoute(array(), 'default', true);
  }

}