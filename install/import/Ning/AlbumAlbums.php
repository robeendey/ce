<?php

class Install_Import_Ning_AlbumAlbums extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-members-local.json';

  protected $_fromFileAlternate = 'ning-members.json';

  protected $_toTable = 'engine4_album_albums';

  protected $_priority = 900;

  protected function  _translateRow(array $data, $key = null)
  {
    return false;


    $userIdentity = $this->getUserMap($data['contributorName']);
    
    $newData = array();

    $newData['album_id'] = $userIdentity;
    $newData['title'] = 'My Photos';
    $newData['description'] = '';
    $newData['owner_type'] = 'user';
    $newData['owner_id'] = $userIdentity;
    $newData['creation_date'] = $this->_translateTime($data['createdDate']);
    $newData['modified_date'] = $this->_translateTime($data['createdDate']);
    $newData['search'] = 1;

    // privacy
    $this->_insertPrivacy('album', $newData['album_id'], 'view');
    $this->_insertPrivacy('album', $newData['album_id'], 'comment');
    
    return $newData;
  }
}