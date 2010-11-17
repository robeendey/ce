<?php

class Install_Import_Ning_EventComments extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-events-local.json';

  protected $_fromFileAlternate = 'ning-events.json';

  protected $_toTable = 'engine4_core_comments';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !isset($data['comments']) || !is_array($data['comments']) || count($data['comments']) < 1 ) {
      return false;
    }

    $eventOwnerIdentity = $this->getUserMap($data['contributorName']);
    $eventIdentity = $key + 1;

    foreach( $data['comments'] as $commentKey => $commentData ) {
      $commentUserIdentity = $this->getUserMap($commentData['contributorName']);

      // Insert into comments?
      $this->getToDb()->insert($this->getToTable(), array(
        'resource_type' => 'event',
        'resource_id' => $eventIdentity,
        'poster_type' => 'user',
        'poster_id' => $commentUserIdentity,
        'body' => $commentData['description'],
        'creation_date' => $this->_translateTime($commentData['createdDate']),
      ));

      // Insert into feed
      $this->getToDb()->insert('engine4_activity_actions', array(
        'type' => 'post',
        'subject_type' => 'user',
        'subject_id' => $commentUserIdentity,
        'object_type' => 'event',
        'object_id' => $eventIdentity,
        'body' => $commentData['description'],
        'date' => $this->_translateTime($commentData['createdDate']),
      ));
      $action_id = $this->getToDb()->lastInsertId();

      // Insert into stream table
      $targetTypes = array(
          'owner' => $eventOwnerIdentity,
          'parent' => $eventOwnerIdentity,
          'members' => $eventOwnerIdentity,
          'registered' => 0,
          'everyone' => 0,
      );
      foreach( $targetTypes as $targetType => $targetIdentity ) {
        $this->getToDb()->insert('engine4_activity_stream', array(
          'target_type' => $targetType,
          'target_id' => $targetIdentity,
          'subject_type' => 'user',
          'subject_id' => $commentUserIdentity,
          'object_type' => 'event',
          'object_id' => $eventIdentity,
          'type' => 'post',
          'action_id' => $action_id,
        ));
      }
    }

    return false;
  }
}