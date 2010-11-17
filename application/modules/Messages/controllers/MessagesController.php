<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: MessagesController.php 7592 2010-10-06 23:13:03Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Messages_MessagesController extends Core_Controller_Action_User
{
  protected $_navigation;

  protected $_form;

  public function init()
  {
    $this->_helper->requireUser();
    $this->_helper->requireAuth()->setAuthParams('messages', null, 'create');
  }
  
  public function inboxAction()
  {
    $this->view->navigation = $this->getNavigation();
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getInboxPaginator($viewer);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->unread = $this->_helper->api()->messages()->getUnreadMessageCount($viewer);
  }

  public function outboxAction()
  {
    $this->view->navigation = $this->getNavigation();
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getOutboxPaginator($viewer);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $this->view->unread = $this->_helper->api()->messages()->getUnreadMessageCount($viewer);
  }

  public function viewAction()
  {
    $this->view->navigation = $this->getNavigation();
    $id = $this->_getParam('id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Get conversation info
    $this->view->conversation = $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

    // Make sure the user is part of the conversation
    if( !$conversation || !$conversation->hasRecipient($viewer) ) {
      return $this->_forward('inbox');
    }
    
    $this->view->recipients = $recipients = $conversation->getRecipients();

    $blocked = false;
    $blocker = "";
    foreach($recipients as $recipient){
      if ($viewer->isBlockedBy($recipient)){
        $blocked = true;
        $blocker = $recipient;
      }
    }
    $this->view->blocked = $blocked;
    $this->view->blocker = $blocker;

    // Assign the composing junk
    $composePartials = array();
    foreach( Zend_Registry::get('Engine_Manifest') as $data )
    {
      if( empty($data['composer']) ) continue;
      foreach( $data['composer'] as $type => $config )
      {
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;

    
    // Process form
    $this->view->form = $form = new Messages_Form_Reply();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {
      $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
      $db->beginTransaction();

      try
      {
        // Try attachment getting stuff
        $attachment = null;
        $attachmentData = $this->getRequest()->getParam('attachment');
        if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
          $type = $attachmentData['type'];
          $config = null;
          foreach( Zend_Registry::get('Engine_Manifest') as $data )
          {
            if( !empty($data['composer'][$type]) )
            {
              $config = $data['composer'][$type];
            }
          }
          if( $config ) {
            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onAttach'.ucfirst($type);
            $attachment = $plugin->$method($attachmentData);

            $parent = $attachment->getParent();
            if($parent->getType() === 'user'){
              $attachment->search = 0;
              $attachment->save();
            }
            else {
              $parent->search = 0;
              $parent->save();
            }
            
          }
        }

        $values = $form->getValues();
        $values['conversation'] = (int) $id;

        $conversation->reply(
          $viewer,
          $values['body'],
          $attachment
        );
        /*
        $this->_helper->api()->messages()->replyMessage(
          $viewer,
          $values['conversation'],
          $values['body'],
          $attachment
        );
         * 
         */

        // Send notifications
        foreach( $recipients as $user )
        {
          if( $user->getIdentity() == $viewer->getIdentity() )
          {
            continue;
          }
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
            $user,
            $viewer,
            $conversation,
            'message_new'
          );
        }

        // Increment messages counter
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');

        $db->commit();
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $form->populate(array('body' => ''));
      return $this->_helper->redirector->gotoRoute(array('action' => 'view', 'id' => $id));
    }

    // Make sure to load the messages after posting :P
    $this->view->messages = $messages = $conversation->getMessages($viewer);

    $conversation->setAsRead($viewer);
  }

  public function composeAction()
  {
    $multi = $this->_getParam('multi', null);
    $to = $this->_getParam('to', null);
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Messages_Form_Compose();
    $friends = $this->_helper->api()->user()->getViewer()->membership()->getMembers();

    $data = array();
    foreach( $friends as $friend )
    {
      $friend_photo = $this->view->itemPhoto($friend, 'thumb.icon');
      $data[] = array('label' => $friend->getTitle(), 'id' => $friend->getIdentity(), 'photo' => $friend_photo);
      
    }
    $data = Zend_Json::encode($data);
    $this->view->friends = $data;

    if( $to !== null && empty($multi))
    {
      $toUser = $this->_helper->api()->user()->getUser($to);
      if(!$viewer->isBlockedBy($toUser)) {
        $this->view->toUser = $toUser;
        $form->toValues->setValue($to);
      }
    }

    // logic for handling multiple recipients (i.e. messaging group members)
    if( !empty($multi))
    {

      // get item using multi + to (which is the id of the item)
      $item = Engine_Api::_()->getItem($multi, $to);

      if ($item->isOwner($viewer)){
        // get membership. put the id's into a comma separated value
        $select = $item->membership()->getMembersObjectSelect();
        $members = Zend_Paginator::factory($select);

        $multi_ids = '';
        foreach($members as $member){
          // leave out the viewer in the recipient array
          if($member->getIdentity() != $viewer->getIdentity()){
            if (!$multi_ids){
              $multi_ids = $member->getIdentity();
            }
            else $multi_ids = $multi_ids.",".$member->getIdentity();
          }
        }

        // make sure the viewer/owner is not the only one in the list
        if($multi_ids){
          $this->view->multi = $multi;
          $this->view->multi_name = $item->getTitle();
          $this->view->multi_ids = $multi_ids;
          $form->toValues->setValue($multi_ids);
        }
      }
    }
    // Assign the composing stuff
    $composePartials = array();
    foreach( Zend_Registry::get('Engine_Manifest') as $data )
    {
      if( empty($data['composer']) ) continue;
      foreach( $data['composer'] as $type => $config )
      {
        $composePartials[] = $config['script'];
      }
    }
    $this->view->composePartials = $composePartials;


    // Check method/data
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
        $type = $attachmentData['type'];
        $config = null;
        foreach( Zend_Registry::get('Engine_Manifest') as $data )
        {
          if( !empty($data['composer'][$type]) )
          {
            $config = $data['composer'][$type];
          }
        }
        if( $config ) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach'.ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if($parent->getType() === 'user'){
            $attachment->search = 0;
            $attachment->save();
          }
          else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }
      
      $viewer = $this->_helper->api()->user()->getViewer();
      $values = $form->getValues();
      $recipients = preg_split('/[,. ]+/', $values['toValues']);

      // limit recipients if it is not a special list of members
      if(empty($multi)) $recipients = array_slice($recipients, 0, 10); // Slice down to 10

      // clean the recipients for repeating ids
      // this can happen if recipient is selected and then a friend list is selected
      $recipients = array_unique($recipients);

      $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);

      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment
      );

      /*
      $conversation_id = $this->_helper->api()->messages()->sendMessage(
        $viewer,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment
      );
       * 
       */

      foreach( $recipientsUsers as $user )
      {
        if( $user->getIdentity() == $viewer->getIdentity() )
        {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
      
      $db->commit();

      return $this->_forward('success', 'utility', 'core', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.')),
        'redirect' => $this->getFrontController()->getRouter()->assemble(array('action' => 'inbox'))
      ));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  
  public function successAction()
  {
    
  }

  public function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    $message_ids = $this->view->message_ids = $this->getRequest()->getParam('message_ids');
    if (!$this->getRequest()->isPost())
      return;

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->deleted_conversation_ids = array();
    
    $db = $this->_helper->api()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    try {
      foreach (explode(',', $message_ids) as $message_id) {
        $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
        //$recipients = Engine_Api::_()->getApi('core', 'messages')->getConversationRecipientsInfo($message_id);
        foreach ($recipients as $r) {
          if ($viewer_id == $r->user_id) {
            $this->view->deleted_conversation_ids[] = $r->conversation_id;
            $r->inbox_deleted  = true;
            $r->outbox_deleted = true;
            $r->save();
          }
        }
      }
      $this->view->success = true;
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

  }
  
  public function getNavigation()
  {
    if( is_null($this->_navigation) )
    {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages(array(
        array(
          'label' => 'Inbox',
          'route' => 'messages_general',
          'action' => 'inbox',
          'controller' => 'messages',
          'module' => 'messages'
        ),
        array(
          'label' => 'Sent Messages',
          'route' => 'messages_general',
          'action' => 'outbox',
          'controller' => 'messages',
          'module' => 'messages'
        ),
        array(
          'label' => 'Compose Message',
          'route' => 'messages_general',
          'action' => 'compose',
          'controller' => 'messages',
          'module' => 'messages'
        )
      ));
    }
    return $this->_navigation;
  }
}