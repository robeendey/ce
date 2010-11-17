<?php

abstract class Install_Import_Ning_Abstract extends Install_Import_JsonAbstract
{
  static protected $_userMap;

  static protected $_groupMap;

  static protected $_eventMap;

  static protected $_discussionMap;

  static protected $_videoMap;

  static protected $_levels;

  static protected $_updateUsers;



  // User map
  
  public function getUserMap($key)
  {
    if( isset(self::$_userMap[$key]) ) {
      return self::$_userMap[$key];
    } else {
      throw new Engine_Exception('No user mapping detected');
    }
  }

  public function setUserMap($key, $userIdentity)
  {
    self::$_userMap[$key] = $userIdentity;
    return $this;
  }



  // Group map

  public function getGroupMap($key)
  {
    if( isset(self::$_groupMap[$key]) ) {
      return self::$_groupMap[$key];
    } else {
      throw new Engine_Exception('No group mapping detected');
    }
  }

  public function setGroupMap($key, $groupIdentity)
  {
    self::$_groupMap[$key] = $groupIdentity;
    return $this;
  }



  // Event map

  public function getEventMap($key)
  {
    if( isset(self::$_eventMap[$key]) ) {
      return self::$_eventMap[$key];
    } else {
      throw new Engine_Exception('No event mapping detected');
    }
  }

  public function setEventMap($key, $eventIdentity)
  {
    self::$_eventMap[$key] = $eventIdentity;
    return $this;
  }



  // Discussion map

  public function getDiscussionMap($key)
  {
    if( isset(self::$_discussionMap[$key]) ) {
      return self::$_discussionMap[$key];
    } else {
      throw new Engine_Exception('No discussion mapping detected');
    }
  }

  public function setDiscussionMap($key, $topicIdentity)
  {
    self::$_discussionMap[$key] = $topicIdentity;
    return $this;
  }



  // Discussion map

  public function getVideoMap($key)
  {
    if( isset(self::$_videoMap[$key]) ) {
      return self::$_videoMap[$key];
    } else {
      throw new Engine_Exception('No video mapping detected');
    }
  }

  public function setVideoMap($key, $topicIdentity)
  {
    self::$_videoMap[$key] = $topicIdentity;
    return $this;
  }



  // Update users

  public function setUpdateUser($user_id)
  {
    self::$_updateUsers[] = $user_id;
    return $this;
  }

  public function isUpdateUser($user_id)
  {
    return in_array($user_id, (array) self::$_updateUsers);
  }



  // MIsc

  
  public function getLevel($type)
  {
    $types = explode(' ', $type);
    if( in_array('owner', $types) ) {
      return 1;
    } else {
      return 4;
    }
  }

  protected function _insertPrivacy($resourceType, $resourceId, $action, $value = 'everyone')
  {
    $this->getToDb()->insert('engine4_authorization_allow', array(
      'resource_type' => $resourceType,
      'resource_id' => $resourceId,
      'action' => $action,
      'role' => $value,
      'value' => 1,
    ));
  }
}