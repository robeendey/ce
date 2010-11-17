<?php

class Install_Import_Ning_AnnouncementAnnouncements extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-notes-local.json';

  protected $_fromFileAlternate = 'ning-notes.json';

  protected $_toTable = 'engine4_announcement_announcements';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);
    
    $newData = array();

    //$newRow['announcement_id'] = $row['announcement_id'];
    $newData['creation_date'] = $this->_translateTime(strtotime($data['createdDate']));
    $newData['modified_date'] = $this->_translateTime(strtotime($data['updatedDate']));
    $newData['title'] = $data['title'];
    $newData['body'] = $data['description'];
    $newData['user_id'] = $userIdentity;

    return $newData;
  }
}