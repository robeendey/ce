<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Plugin_Core
{
  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User ) {

      // Remove from online users
      $onlineUsersTable = Engine_Api::_()->getDbtable('online', 'user');
      $onlineUsersTable->delete(array(
        'user_id = ?' => $payload->getIdentity(),
      ));

      // Remove friends
      $payload->membership()->removeAllUserFriendship();

      // Remove all cases user is in a friend list
      $payload->lists()->removeUserFromLists();

      // Remove all friend list created by the user
      $payload->lists()->removeUserLists();
    }
  }

  public function getAdminNotifications($event)
  {
    $userTable = Engine_Api::_()->getItemTable('user');
    $select = new Zend_Db_Select($userTable->getAdapter());
    $select->from($userTable->info('name'), 'COUNT(user_id) as count')
      ->where('enabled = ?', 0)
      ;

    $data = $select->query()->fetch();
    if( empty($data['count']) ) {
      return;
    }

    $translate = Zend_Registry::get('Zend_Translate');
    $message = vsprintf($translate->translate(array(
      'There is <a href="%s">%d new member</a> awaiting your approval.',
      'There are <a href="%s">%d new members</a> awaiting your approval.',
      $data['count']
    )), array(
      Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'user', 'controller' => 'manage'), 'admin_default', true) . '?enabled=0',
      $data['count'],
    ));

    $event->addResponse($message);
  }

  public function onUserCreateAfter($event)
  {
    $payload = $event->getPayload();
    if( $payload instanceof User_Model_User && 'none' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
      $facebook = User_Model_DbTable_Facebook::getFBInstance();
      if ($facebook->getSession()) {
        try {
          $facebook->api('/me');
          $table = Engine_Api::_()->getDbtable('facebook', 'user');
          $row = $table->fetchRow(array('user_id = ?'=>$payload->getIdentity()));
          if (!$row) {
            $row = Engine_Api::_()->getDbtable('facebook', 'user')->createRow();
            $row->user_id = $payload->getIdentity();
          }
          $row->facebook_uid = $facebook->getUser();
          $row->save();
        } catch (Exception $e) {}
      }
    }
  }
}