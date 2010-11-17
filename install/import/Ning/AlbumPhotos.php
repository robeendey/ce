<?php

class Install_Import_Ning_AlbumPhotos extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-photos-local.json';

  protected $_fromFileAlternate = 'ning-photos.json';

  protected $_toTable = 'engine4_album_photos';

  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);
    $photoIdentity = $key + 1;

    // Lookup album
    $albumIdentity = $this->getToDb()->select()
      ->from('engine4_album_albums', 'album_id')
      ->where('owner_id = ?', $userIdentity)
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    if( !$albumIdentity ) {
      $this->getToDb()->insert('engine4_album_albums', array(
        'title' => 'My Photos',
        'description' => '',
        'owner_type' => 'user',
        'owner_id' => $userIdentity,
        'creation_date' => $this->_translateTime($data['createdDate']),
        'modified_date' => $this->_translateTime($data['updatedDate']),
        'search' => 1,
      ));
      $albumIdentity = $this->getToDb()->lastInsertId();
      
      // privacy
      $this->_insertPrivacy('album', $albumIdentity, 'view');
      $this->_insertPrivacy('album', $albumIdentity, 'comment');
    }

    
    $newData = array();

    $newData['photo_id'] = $photoIdentity;
    $newData['title'] = $data['title'];
    $newData['creation_date'] = $this->_translateTime($data['createdDate']);
    $newData['modified_date'] = $this->_translateTime($data['updatedDate']);
    $newData['collection_id'] = $albumIdentity;
    $newData['owner_type'] = 'album';
    $newData['owner_id'] = $albumIdentity;
    $newData['comment_count'] = ( isset($data['comments']) ? count($data['comments']) : 0 );
    
    // Import file
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $data['photoUrl'];

    if( file_exists($file) ) {
      $file_id = $this->_translatePhoto($file, array(
        'parent_type' => 'album_photo',
        'parent_id' => $photoIdentity,
        'user_id' => $userIdentity,
      ));

      if( $file_id ) {
        $newData['file_id'] = $file_id;

        // Set cover
        // Note: albums has to be run first
        $this->getToDb()->update('engine4_album_albums', array(
          'photo_id' => $file_id,
        ), array(
          'album_id = ?' => $userIdentity,
          'photo_id = ?' => 0,
        ));
      }

    }

    return $newData;
  }
}