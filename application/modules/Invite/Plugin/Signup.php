<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Signup.php 7559 2010-10-05 20:26:34Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Invite_Plugin_Signup
{
  public function onUserCreateAfter($payload)
  {
    $user = $payload->getPayload();
    $session = new Zend_Session_Namespace('invite');
    $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
    $isEligible = Engine_Api::_()->getApi('settings', 'core')->user_friends_eligible;
    //$inviteTable = new Zend_Db_Table();

    // Get codes
    $codes = array();
    if( !empty($session->invite_code) ) {
      $codes[] = $session->invite_code;
    }
    if( !empty($session->signup_code) ) {
      $codes[] = $session->signup_code;
    }
    $codes = array_unique($codes);

    // Get emails
    $emails = array();
    if( !empty($session->invite_email) ) {
      $emails[] = $session->invite_email;
    }
    if( !empty($session->signup_email) ) {
      $emails[] = $session->signup_email;
    }
    $emails = array_unique($emails);

    // Nothing, exit now
    if( empty($codes) && empty($emails) ) {
      return;
    }
    
    // Get related invites
    $select = $inviteTable->select();

    if( !empty($codes) ) {
      $select->orWhere('code IN(?)', $codes);
    }

    if( !empty($emails) ) {
      $select->orWhere('recipient IN(?)', $emails);
    }
    
    $updateInviteIds = array();
    $befriendUserIds = array();
    foreach( $inviteTable->fetchAll($select) as $invite ) {
      $befriendUserIds[] = $invite->user_id;

      // Set new user if if not already
      if( 0 == $invite->new_user_id ) {
        $updateInviteIds[] = $invite->id;
      }
    }

    // Update invites
    if( !empty($updateInviteIds) ) {
      $inviteTable->update(array(
        'new_user_id' => $user->getIdentity(),
      ), array(
        'id IN(?)' => $updateInviteIds,
        'new_user_id = ?' => 0,
      ));
    }
    
    // Befriend users
    if( $isEligible && !empty($befriendUserIds) ) {
      $befriendUsers = Engine_Api::_()->getItemTable('user')->find($befriendUserIds);
      if( !empty($befriendUsers) ) {
        $activity = Engine_Api::_()->getDbtable('notifications', 'activity');
        foreach( $befriendUsers as $befriendUser ) {
          $user->membership()
            ->addMember($befriendUser)
            ->setUserApproved($befriendUser);
          $activity->addNotification($user, $befriendUser, $user, 'friend_request');
        }
      }
    }


    // Clean session
    $session->unsetAll();
  }
  
}

