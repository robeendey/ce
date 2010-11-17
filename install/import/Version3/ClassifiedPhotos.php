<?php

class Install_Import_Version3_ClassifiedPhotos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_classifiedmedia';

  protected $_fromJoins = array(
    'se_classifiedalbums' => 'classifiedalbum_id=classifiedmedia_classifiedalbum_id',
    'se_classifieds' => 'classifiedalbum_classified_id=classified_id',
  );

  protected $_toTable = 'engine4_classified_photos';

  protected $_priority = 90;

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();
    
    $newData['photo_id'] = $data['classifiedmedia_id'];
    $newData['album_id'] = $data['classifiedmedia_classifiedalbum_id'];
    $newData['classified_id'] = @$data['classifiedalbum_classified_id'];
    $newData['user_id'] = @$data['classified_user_id'];
    $newData['title'] = (string) @$data['classifiedmedia_title'];
    $newData['description'] = (string) @$data['classifiedmedia_desc'];
    $newData['collection_id'] = $data['classifiedmedia_classifiedalbum_id'];
    $newData['creation_date'] = $this->_translateTime($data['classifiedmedia_date']);
    $newData['modified_date'] = $this->_translateTime($data['classifiedmedia_date']);

    // Import file
    $file = $this->_getFromUserDir(
      $data['classified_id'],
      'uploads_classified',
      $data['classifiedmedia_id'] . '.' . $data['classifiedmedia_ext']
    );

    if( file_exists($file) ) {
      try {
        if( $this->getParam('resizePhotos', true) ) {
          $file_id = $this->_translatePhoto($file, array(
            'parent_type' => 'classified_photo',
            'parent_id' => $data['classifiedmedia_id'],
            'user_id' => @$data['classified_user_id'],
          ));
        } else {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'classified_photo',
            'parent_id' => $data['classifiedmedia_id'],
            'user_id' => @$data['classified_user_id'],
          ), true);
        }
      } catch( Exception $e ) {
        $file_id = null;
        $this->_warning($e->getMessage(), 1);
      }

      if( $file_id ) {
        $newData['file_id'] = $file_id;

        // Set cover
        // Note: albums has to be run first
        $coverPhotoId = @$data['classifiedalbum_cover'];

        if( $coverPhotoId && $coverPhotoId == $data['classifiedmedia_id'] ) {
          $this->getToDb()->update('engine4_classified_albums', array(
            'photo_id' => $file_id,
          ), array(
            'album_id = ?' => $data['classifiedmedia_classifiedalbum_id'],
          ));
        }

      }
    }

    // search
    if( @$data['classifiedalbum_search'] ) {
      $this->_insertSearch('classified_photo', @$newData['photo_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_classifiedmedia` (
*  `classifiedmedia_id` int(10) unsigned NOT NULL auto_increment,
*  `classifiedmedia_classifiedalbum_id` int(10) unsigned NOT NULL default '0',
*  `classifiedmedia_date` int(11) NOT NULL default '0',
*  `classifiedmedia_title` varchar(128) collate utf8_unicode_ci default '',
*  `classifiedmedia_desc` text collate utf8_unicode_ci,
-  `classifiedmedia_ext` varchar(8) collate utf8_unicode_ci NOT NULL default '',
-  `classifiedmedia_filesize` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedmedia_id`),
  KEY `INDEX` (`classifiedmedia_classifiedalbum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_photos` (
*  `photo_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `album_id` int(11) unsigned NOT NULL,
*  `classified_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `title` varchar(128) NOT NULL,
*  `description` varchar(255) NOT NULL,
*  `collection_id` int(11) unsigned NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `classified_id` (`classified_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */