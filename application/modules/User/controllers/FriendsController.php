<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FriendsController.php 7564 2010-10-05 23:10:16Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_FriendsController extends Core_Controller_Action_User
{
  var $friends_enabled = false;

  public function init()
  {
    // Try to set subject
    $user_id = $this->_getParam('user_id', null);
    if( $user_id && !Engine_Api::_()->core()->hasSubject() )
    {
      $user = Engine_Api::_()->getItem('user', $user_id);
      if( $user )
      {
        Engine_Api::_()->core()->setSubject($user);
      }
    }


    //(new Request({url:'http://10.0.0.11/engine4/index.php/members/friends/cancel/id/2',data:{format:'json'}})).send();
    $this->eligible = Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
    if (!$this->eligible)
    {
      //  die();
    }
  }
  
  public function listAddAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $friend_id = (int) $this->_getParam('friend_id');
    
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if( !$user->getIdentity() || !$friend || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if( $list->has($friend) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already in list');
      return;
    }

    $list->add($friend);
    
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member added to list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listRemoveAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $friend_id = (int) $this->_getParam('friend_id');
    
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    // Check params
    if( !$user->getIdentity() || !$friend || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    // Check if already target status
    if( !$list->has($friend) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Already not in list');
      return;
    }

    $list->remove($friend);

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Member removed from list.');
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listCreateAction()
  {
    $title = (string) $this->_getParam('title');
    $friend_id = (int) $this->_getParam('friend_id');
    $user = Engine_Api::_()->user()->getViewer();
    $friend = Engine_Api::_()->getItem('user', $friend_id);

    if( !$user->getIdentity() || !$title )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->createRow();
    $list->owner_id = $user->getIdentity();
    $list->title = $title;
    $list->save();

    if( $friend && $friend->getIdentity() )
    {
      $list->add($friend);
    }

    $this->view->status = true;
    $this->view->message = 'List created.';
    $this->view->list_id = $list->list_id;
    Engine_Api::_()->core()->setSubject($user);
  }

  public function listDeleteAction()
  {
    $list_id = (int) $this->_getParam('list_id');
    $user = Engine_Api::_()->user()->getViewer();

    // Check params
    if( !$user->getIdentity() || !$list_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check list
    $listTable = Engine_Api::_()->getItemTable('user_list');
    $list = $listTable->find($list_id)->current();
    if( !$list || $list->owner_id != $user->getIdentity() ) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Missing list/not authorized');
    }

    $list->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('List deleted');
  }
  
  public function addAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    $viewer = $this->_helper->api()->user()->getViewer();
    $user = $this->_helper->api()->user()->getUser($user_id);

    // check that user is not trying to befriend 'self'
    if( $viewer->isSelf($user) ){
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You cannot befriend yourself.'))
      ));
    }

    // check that user is already friends with the member
    if( $viewer->membership()->isMember($user)){
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You are already friends with this member.'))
      ));
    }

    // check that user has not blocked the member
    if( $viewer->isBlocked($user)){
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Friendship request was not sent because you blocked this member.'))
      ));
    }
    

    // Make form
    $this->view->form = $form = new User_Form_Friends_Add();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {

      // check friendship verification settings
      // add membership if allowed to have unverified friendships
      //$user->membership()->setUserApproved($viewer);

      // else send request
      $user->membership()->addMember($viewer)->setUserApproved($viewer);


      // send out different notification depending on what kind of friendship setting admin has set
      /*('friend_accepted', 'user', 'You and {item:$subject} are now friends.', 0, ''),
        ('friend_request', 'user', '{item:$subject} has requested to be your friend.', 1, 'user.friends.request-friend'),
        ('friend_follow_request', 'user', '{item:$subject} has requested to add you as a friend.', 1, 'user.friends.request-friend'),
        ('friend_follow', 'user', '{item:$subject} has added you as a friend.', 1, 'user.friends.request-friend'),
       */
      

      // if one way friendship and verification not required
      if(!$user->membership()->isUserApprovalRequired()&&!$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow');

        $message = Zend_Registry::get('Zend_Translate')->_("You are now following this member.");
      }

      // if two way friendship and verification not required
      else if(!$user->membership()->isUserApprovalRequired()&&$user->membership()->isReciprocal()){
        // Add activity
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
        Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');

        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');
        $message = Zend_Registry::get('Zend_Translate')->_("You are now friends with this member.");
      }

      // if one way friendship and verification required
      else if(!$user->membership()->isReciprocal()){
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }

      // if two way friendship and verification required
      else if($user->membership()->isReciprocal())
      {
        // Add notification
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_request');
        $message = Zend_Registry::get('Zend_Translate')->_("Your friend request has been sent.");
      }


      $this->view->status = true;

      $db->commit();

      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been sent.');
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => true,
          'messages' => array($message)
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function cancelAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }
    
    // Make form
    $this->view->form = $form = new User_Form_Friends_Cancel();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }
    
    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      // Set the request as handled if it was a follow request
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.');
      $this->_forward('success','utility','core', Array('smoothboxClose'=>true, 'parentRefresh'=>true, 'messages'=>Array(Zend_Registry::get('Zend_Translate')->_('Your friend request has been cancelled.'))));

    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function confirmAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Confirm();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->setUserApproved($viewer);

      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends', '{item:$object} is now friends with {item:$subject}.');
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $user, 'friends', '{item:$object} is now friends with {item:$subject}.');
      
      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_accepted');

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      // Increment friends counter
      // @todo make sure this works fine for following
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('user.friendships');

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You are now friends with %s');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array($message)
          //'You have accepted this friend request.'
        
      ));

    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function followAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Confirm();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);

      $user->membership()->setFollowApproved($viewer);

      // Add activity
      Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($user, $viewer, 'friends_follow', '{item:$object} is now following {item:$subject}.');

      // Add notification
      Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($user, $viewer, $user, 'friend_follow_accepted');

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('%s is now following you');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array($message)
          //'You have accepted this friend request.'

      ));

    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function rejectAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Reject();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();
      $message = Zend_Registry::get('Zend_Translate')->_('You ignored a friend request from %s');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(
          $this->view->message
          //'You have ignored this friend request.'
        )
      ));

    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  
  public function ignoreAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireSubject()->isValid() ) return;

    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);
    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }

    // Make form
    $this->view->form = $form = new User_Form_Friends_Reject();

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);

      $user->membership()->removeFollow($viewer);

      // Set the request as handled
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $viewer, $user, 'friend_follow_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }

      $db->commit();

      $message = Zend_Registry::get('Zend_Translate')->_('You ignored %s\'s request to follow you');
      $message = sprintf($message, $user->__toString());

      $this->view->status = true;
      $this->view->message = $message;
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(
          $this->view->message
          //'You have ignored this friend request.'
        )
      ));

    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }
  public function removeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get id of friend to add
    $user_id = $this->_getParam('user_id', null);

    if( null == $user_id )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No member specified');
      return;
    }


    // Make form
    $this->view->form = $form = new User_Form_Friends_Remove();
    if( !$this->getRequest()->isPost() )
      {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No action taken');
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('membership', 'user')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $user = $this->_helper->api()->user()->getUser($user_id);
      
      $user->membership()->removeMember($viewer);
      $user->lists()->removeFriendFromLists($viewer);

      // Set the request as handled - this may not be neccesary here
      $notification = Engine_Api::_()->getDbtable('notifications', 'activity')->getNotificationBySubjectAndType(
        $user, $viewer, 'friend_request');
      if( $notification )
      {
        $notification->mitigated = true;
        $notification->read = 1;
        $notification->save();
      }
      
      $db->commit();

      $this->view->status = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.');
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'messages' => array(
          Zend_Registry::get('Zend_Translate')->_('This person has been removed from your friends.')
        )
      ));
    }
    catch( Exception $e )
    {
      $db->rollBack();

      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error has occurred.');
      $this->view->exception = $e->__toString();
    }
  }

  public function suggestAction()
  {
    $data = array();
    if( $this->_helper->requireUser()->checkRequire() )
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $table = Engine_Api::_()->getItemTable('user');
      $select = $this->_helper->api()->user()->getViewer()->membership()->getMembersObjectSelect();

      if( $this->_getParam('includeSelf', false) ) {
        $data[] = array(
          'type' => 'user',
          'id' => $viewer->getIdentity(),
          'guid' => $viewer->getGuid(),
          'label' => $viewer->getTitle() . ' (you)',
          'photo' => $this->view->itemPhoto($viewer, 'thumb.icon'),
          'url' => $viewer->getHref(),
        );
      }

      if( 0 < ($limit = (int) $this->_getParam('limit', 10)) )
      {
        $select->limit($limit);
      }

      if( null !== ($text = $this->_getParam('search', $this->_getParam('value'))))
      {
        $select->where('`'.$table->info('name').'`.`displayname` LIKE ?', '%'. $text .'%');
      }
      $ids = array();
      foreach( $select->getTable()->fetchAll($select) as $friend )
      {
        $data[] = array(
          'type'  => 'user',
          'id'    => $friend->getIdentity(),
          'guid'  => $friend->getGuid(),
          'label' => $friend->getTitle(),
          'photo' => $this->view->itemPhoto($friend, 'thumb.icon'),
          'url'   => $friend->getHref(),
        );
        $ids[] = $friend->getIdentity();
        $friend_data[$friend->getIdentity()] = $friend->getTitle();
      }

      // first get friend lists created by the user
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));
      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
        $listArray[$list->list_id] = $list->title;
      }

      // check if user has friend lists
      if($listIds){
        // get list of friend list + friends in the list
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $uName = Engine_Api::_()->getDbtable('users')->info('name');
        $iName  = $listItemTable->info('name');

        $listItemSelect = $listItemTable->select()
          ->setIntegrityCheck(false)
          ->from($iName, array($iName.'.listitem_id', $iName.'.list_id', $iName.'.child_id',$uName.'.displayname'))
          ->joinLeft($uName, "$iName.child_id = $uName.user_id")
          //->group("$iName.child_id")
          ->where('list_id IN(?)', $listIds);

        $listItems = $listItemTable->fetchAll($listItemSelect);

        $listsByUser = array();
        foreach( $listItems as $listItem ) {
          $listsByUser[$listItem->list_id][$listItem->user_id]= $listItem->displayname ;
        }
        
        foreach ($listArray as $key => $value){
          if (!empty($listsByUser[$key])){
            $data[] = array(
              'type' => 'list',
              'friends' => $listsByUser[$key],
              'label' => $value,
            );
          }
        }
      }
      
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }

  
  public function requestFriendAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }

  public function requestFollowAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }

}
