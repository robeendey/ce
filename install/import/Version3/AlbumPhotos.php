<?php

class Install_Import_Version3_AlbumPhotos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_media';

  protected $_fromJoins = array(
    'se_albums' => 'album_id=media_album_id',
  );

  protected $_toTable = 'engine4_album_photos';

  protected $_priority = 90;
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['photo_id'] = $data['media_id'];
    $newData['title'] = (string) @$data['media_title'];
    $newData['description'] = (string) @$data['media_desc'];
    $newData['creation_date'] = $this->_translateTime($data['media_date']);
    $newData['modified_date'] = $this->_translateTime($data['media_date']);
    $newData['collection_id'] = $data['media_album_id'];
    $newData['owner_type'] = 'album';
    $newData['owner_id'] = $data['media_album_id'];
    $newData['comment_count'] = $data['media_totalcomments'];

    // Import file
    $file = $this->_getFromUserDir(
      $data['album_user_id'],
      'uploads_user',
      $data['media_id'] . '.' . $data['media_ext']
    );

    if( file_exists($file) ) {
      try {
        if( $this->getParam('resizePhotos', true) ) {
          $file_id = $this->_translatePhoto($file, array(
            'parent_type' => 'album_photo',
            'parent_id' => $data['media_id'],
            'user_id' => $data['album_user_id'],
          ));
        } else {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'album_photo',
            'parent_id' => $data['media_id'],
            'user_id' => $data['album_user_id'],
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
        $coverPhotoId = @$data['album_cover'];

        if( $coverPhotoId && $coverPhotoId == $data['media_id'] ) {
          $this->getToDb()->update('engine4_album_albums', array(
            'photo_id' => $file_id,
          ), array(
            'album_id = ?' => $data['media_album_id'],
          ));
        }
      }

    }
    
    // search
    if( @$data['album_search'] ) {
      $this->_insertSearch('album_photo', @$newData['photo_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_media` (
*  `media_id` int(10) unsigned NOT NULL auto_increment,
*  `media_album_id` int(10) unsigned NOT NULL default '0',
*  `media_date` int(14) NOT NULL default '0',
*  `media_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `media_desc` text collate utf8_unicode_ci,
-  `media_ext` varchar(8) collate utf8_unicode_ci NOT NULL default '',
-  `media_filesize` bigint(20) unsigned NOT NULL default '0',
  `media_order` int(1) NOT NULL default '0',
*  `media_totalcomments` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`media_id`),
  KEY `INDEX` (`media_album_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_album_photos` (
*  `photo_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(128) NOT NULL,
*  `description` mediumtext NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `collection_id` int(11) unsigned NOT NULL,
*  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `owner_id` int(11) unsigned NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
-  `view_count` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`photo_id`),
  KEY `collection_id` (`collection_id`),
  KEY `owner_type` (`owner_type`, `owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */