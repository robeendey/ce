<?php

class Install_Import_Version3_CoreAdphotos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_ads';

  protected $_toTable = 'engine4_core_adphotos';

  protected function  _translateRow(array $data, $key = null)
  {
    if( empty($data['ad_filename']) ) {
      return false;
    }
    
    // get file path
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR
      . 'uploads_admin' . DIRECTORY_SEPARATOR
      . 'ads' . DIRECTORY_SEPARATOR
      . $data['ad_filename'];

    // make adphoto
    $this->getToDb()->insert('engine4_core_adphotos', array(
      'ad_id' => $data['ad_id'],
      'title' => '',
      'description' => '',
      'file_id' => 0,
      'creation_date' => $this->_translateTime(@filemtime($file)),
      'modified_date' => $this->_translateTime(@filemtime($file)),
    ));
    $adphoto_id = $this->getToDb()->lastInsertId();

    // Create ad photo
    $file_id = $this->_translateFile($file, array(
      'parent_type' => 'core_adphoto',
      'parent_id' => $adphoto_id,
    ));

    $this->getToDb()->update('engine4_core_adphotos', array(
      'file_id' => $file_id,
    ), array(
      'adphoto_id = ?' => $adphoto_id,
    ));

    // Update
    $this->getToDb()->update('engine4_core_ads', array(
      'photo_id' => $adphoto_id,
    ), array(
      'ad_id = ?' => $data['ad_id'],
    ));

    // Cancel standard
    return false;
  }
}

/*
CREATE TABLE IF NOT EXISTS `engine4_core_adphotos` (
*  `adphoto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `ad_id` int(11) unsigned NOT NULL,
?  `title` varchar(128) NOT NULL,
?  `description` varchar(255) NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`adphoto_id`),
  KEY `ad_id` (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */