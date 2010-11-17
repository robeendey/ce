<?php

class Install_Import_Ning_AlbumPhotoComments extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-photos-local.json';

  protected $_fromFileAlternate = 'ning-photos.json';

  protected $_toTable = 'engine4_core_comments';

  protected function  _translateRow(array $data, $key = null)
  {
    if( !isset($data['comments']) || !is_array($data['comments']) || count($data['comments']) < 1 ) {
      return false;
    }
    
    $photoIdentity = $key + 1;

    foreach( $data['comments'] as $commentKey => $commentData ) {
      $commentUserIdentity = $this->getUserMap($commentData['contributorName']);
      $this->getToDb()->insert($this->getToTable(), array(
        'resource_type' => 'album_photo',
        'resource_id' => $photoIdentity,
        'poster_type' => 'user',
        'poster_id' => $commentUserIdentity,
        'body' => $commentData['description'],
        'creation_date' => $this->_translateTime($commentData['createdDate']),
      ));
    }

    return false;
  }
}