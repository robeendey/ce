<?php

class Install_Import_Ning_UserComments extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_core_comments';

  protected function  _translateRow(array $data, $key = null)
  {
    if( empty($data['comments']) ) {
      return false;
    }

    $userIdentity = $this->getUserMap($data['contributorName']);

    $comments = $data['comments'];
    foreach( $comments as $commentData ) {
      $commentUserIdentity = $this->getUserMap($commentData['contributorName']);

      // Insert into comments?
      $this->getToDb()->insert('engine4_core_comments', array(
        'resource_type' => 'user',
        'resource_id' => $commentUserIdentity,
        'poster_type' => 'user',
        'poster_id' => $userIdentity,
        'body' => $commentData['description'],
        'creation_date' => $this->_translateTime($commentData['createdDate']),
      ));

      // Insert into feed
      $this->getToDb()->insert('engine4_activity_actions', array(
        'type' => 'post',
        'subject_type' => 'user',
        'subject_id' => $commentUserIdentity,
        'object_type' => 'user',
        'object_id' => $userIdentity,
        'body' => $commentData['description'],
        'date' => $this->_translateTime($commentData['createdDate']),
      ));
      $action_id = $this->getToDb()->lastInsertId();

      // Insert into stream table
      $targetTypes = array(
          'owner' => $userIdentity,
          'parent' => $userIdentity,
          'members' => $userIdentity,
          'registered' => 0,
          'everyone' => 0,
      );
      foreach( $targetTypes as $targetType => $targetIdentity ) {
        $this->getToDb()->insert('engine4_activity_stream', array(
          'target_type' => $targetType,
          'target_id' => $targetIdentity,
          'subject_type' => 'user',
          'subject_id' => $commentUserIdentity,
          'object_type' => 'user',
          'object_id' => $userIdentity,
          'type' => 'post',
          'action_id' => $action_id,
        ));
      }
    }

    return false;
  }
}