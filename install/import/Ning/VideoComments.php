<?php

class Install_Import_Ning_VideoComments extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-videos-local.json';

  protected $_fromFileAlternate = 'ning-videos.json';

  protected $_toTable = 'engine4_core_comments';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !isset($data['comments']) || !is_array($data['comments']) || count($data['comments']) < 1 ) {
      return false;
    }

    $videoOwnerIdentity = $this->getUserMap($data['contributorName']);
    $videoIdentity = $key + 1;

    foreach( $data['comments'] as $commentKey => $commentData ) {
      $commentUserIdentity = $this->getUserMap($commentData['contributorName']);

      // Insert into comments?
      $this->getToDb()->insert($this->getToTable(), array(
        'resource_type' => 'video',
        'resource_id' => $videoIdentity,
        'poster_type' => 'user',
        'poster_id' => $commentUserIdentity,
        'body' => $commentData['description'],
        'creation_date' => $this->_translateTime($commentData['createdDate']),
      ));

      /*
      // Insert into feed
      $this->getToDb()->insert('engine4_activity_actions', array(
        'type' => 'post',
        'subject_type' => 'user',
        'subject_id' => $commentUserIdentity,
        'object_type' => 'video',
        'object_id' => $videoIdentity,
        'body' => $commentData['description'],
        'date' => $this->_translateTime($commentData['createdDate']),
      ));
      $action_id = $this->getToDb()->lastInsertId();

      // Insert into stream table
      $targetTypes = array(
          'owner' => $videoOwnerIdentity,
          'parent' => $videoOwnerIdentity,
          'members' => $videoOwnerIdentity,
          'registered' => 0,
          'everyone' => 0,
      );
      foreach( $targetTypes as $targetType => $targetIdentity ) {
        $this->getToDb()->insert('engine4_activity_stream', array(
          'target_type' => $targetType,
          'target_id' => $targetIdentity,
          'subject_type' => 'user',
          'subject_id' => $commentUserIdentity,
          'object_type' => 'video',
          'object_id' => $videoIdentity,
          'type' => 'post',
          'action_id' => $action_id,
        ));
      }
       * 
       */
    }

    return false;
  }
}