<?php

class Install_Import_Ning_VideoVideos extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-videos-local.json';

  protected $_fromFileAlternate = 'ning-videos.json';

  protected $_toTable = 'engine4_video_videos';

  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);
    $videoIdentity = $key + 1;
    $this->setVideoMap($data['id'], $videoIdentity);

    $newData = array();

    $newData['video_id'] = $videoIdentity;
    $newData['title'] = $data['title'];
    $newData['owner_type'] = 'user';
    $newData['owner_id'] = $userIdentity;
    $newData['search'] = 1;
    $newData['creation_date'] = $this->_translateTime($data['createdDate']);
    $newData['modified_date'] = $this->_translateTime($data['updatedDate']);
    $newData['view_count'] = 0;
    $newData['comment_count'] = count((array) @$data['comments']);
    $newData['status'] = 1;

    // @todo duration, category_id, photo_id
    
    // privacy
    $this->_insertPrivacy('video', $newData['video_id'], 'view');
    $this->_insertPrivacy('video', $newData['video_id'], 'comment');

    // Youtube
    if( !empty($data['embedCode']) && stripos($data['embedCode'], 'youtube.com') !== false ) {
      if( preg_match('/v\/(.+?)(\/|&|$)/', $data['embedCode'], $m) ) {
        $newData['type'] = 1;
        $newData['code'] = $m[1];
      } else {
        throw new Engine_Exception('Unable to parse video embed code - ' . Zend_Json::encode($data['embedCode']));
      }
    }

    // Vimeo
    else if( !empty($data['embedCode']) && stripos($data['embedCode'], 'vimeo.com') !== false ) {
      if( preg_match('/clip_id=(\d+)/', $data['embedCode'], $m) ) {
        $newData['type'] = 2;
        $newData['code'] = $m[1];
      } else {
        throw new Engine_Exception('Unable to parse video embed code - ' . Zend_Json::encode($data['embedCode']));
      }
    }

    // File
    else if( !empty($data['videoAttachmentUrl']) ) {

      $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $data['videoAttachmentUrl'];
      
      // Flash
      if( strtolower(substr($data['videoAttachmentUrl'], -4)) === '.flv' ) {
        $newData['type'] = 3;
        $file_id = $this->_translateFile($file, array(
          'parent_type' => 'video',
          'parent_id' => $videoIdentity,
          'user_id' => $userIdentity,
        ));
        $newData['file_id'] = $file_id;
      } else {
        throw new Engine_Exception('Unsupported file type - ' . $data['videoAttachmentUrl']);
      }
      
    }

    // Wtf
    else
    {
      throw new Engine_Exception('Unknown video type - ' . Zend_Json::encode($data));
    }
    
    return $newData;
  }
}