<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Membership.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Model_DbTable_Membership extends Core_Model_DbTable_Membership
{
  protected $_type = 'user';

  public function isReciprocal()
  {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', true);
  }

  public function isUserApprovalRequired()
  {
    return (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.verification', true);

  }

  public function isResourceApprovalRequired()
  {
    return true;
  }


  // Implement reciprocal

  public function addMember(User_Model_User $resource, User_Model_User $user)
  {
    parent::addMember($user, $resource);
  
    if($this->isReciprocal())
    {
      parent::addMember($resource, $user);
    }
    if(!$this->isUserApprovalRequired()){
      parent::setResourceApproved($user, $resource);
      if($this->isReciprocal())
      {
        parent::setResourceApproved($resource, $user);
      }
    }

    return $this;
  }

  public function removeMember(User_Model_User $resource, User_Model_User $user)
  {
    parent::removeMember($user, $resource);

    if($this->isReciprocal())
    {
      parent::removeMember($resource, $user);
    }

    // remove from all the friend lists

    return $this;
  }

  public function setResourceApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    parent::setResourceApproved($resource, $user);

    if($this->isReciprocal())
    {
      parent::setUserApproved($user, $resource);
    }

    return $this;
  }

  public function setUserApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    // if one way friendship and verification not required
    if(!$this->isUserApprovalRequired()&&!$this->isReciprocal()){
      parent::setUserApproved($user, $resource);
    }
    
    // if two way friendship and verification not required
    if(!$this->isUserApprovalRequired()&&$this->isReciprocal()){
      parent::setUserApproved($user, $resource);
    }

    // if one way friendship and verification required
    if(!$this->isReciprocal()){
      parent::setResourceApproved($user, $resource);
    }

    // if two way friendship and verification required
    if($this->isReciprocal())
    {
      parent::setUserApproved($resource, $user);
      parent::setResourceApproved($user, $resource);
    }

    return $this;
  }

  public function setFollowApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
  {
    parent::setUserApproved($resource, $user);

    return $this;
  }

  public function removeFollow(User_Model_User $resource, User_Model_User $user)
  {
    parent::removeMember($resource, $user);

    return $this;
  }


  public function removeAllUserFriendship(User_Model_User $user){
    // first get all cases where user_id == $user->getIdentity
    $select = $this->getTable()->select()
      ->where('user_id = ?', $user->getIdentity());
    
    $friendships = $this->getTable()->fetchAll($select);
    foreach( $friendships as $friendship ) {
      // if active == 1 get the user corresponding to resource_id and take away the member_count by 1
      if($friendship->active){
        $friend = Engine_Api::_()->getItem('user', $friendship->resource_id);
        $friend->member_count--;
        $friend->save();
      }
      $friendship->delete();
    }

    // get all cases where resource_id == $user->getIdentity
    // remove all   
    $this->getTable()->delete(array(
      'resource_id = ?' => $user->getIdentity()
    ));
  }
}