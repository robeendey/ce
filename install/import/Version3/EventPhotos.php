<?php

class Install_Import_Version3_EventPhotos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_eventmedia';

  protected $_fromJoinTable = 'se_eventalbums';

  protected $_fromJoinCondition = 'eventalbum_id=eventmedia_eventalbum_id';

  protected $_toTable = 'engine4_event_photos';

  protected $_priority = 90;

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['photo_id'] = $data['eventmedia_id'];
    $newData['album_id'] = $data['eventmedia_eventalbum_id'];
    $newData['event_id'] = @$data['eventalbum_event_id'];
    $newData['user_id'] = $data['eventmedia_user_id'];
    $newData['title'] = (string) @$data['eventmedia_title'];
    $newData['description'] = (string) @$data['eventmedia_desc'];
    $newData['collection_id'] = $data['eventmedia_eventalbum_id'];
    $newData['creation_date'] = $this->_translateTime($data['eventmedia_date']);
    $newData['modified_date'] = $this->_translateTime($data['eventmedia_date']);
    //$newData['comment_count'] = $data['eventmedia_totalcomments'];

    // Import file
    $file = $this->_getFromUserDir(
      @$data['eventalbum_event_id'],
      'uploads_event',
      $data['eventmedia_id'] . '.' . $data['eventmedia_ext']
    );

    if( file_exists($file) ) {
      try {
        if( $this->getParam('resizePhotos', true) ) {
          $file_id = $this->_translatePhoto($file, array(
            'parent_type' => 'event_photo',
            'parent_id' => $data['eventmedia_id'],
            'user_id' => $data['eventmedia_user_id'],
          ));
        } else {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'event_photo',
            'parent_id' => $data['eventmedia_id'],
            'user_id' => $data['eventmedia_user_id'],
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
        $coverPhotoId = @$data['eventalbum_cover'];

        if( $coverPhotoId && $coverPhotoId == $data['media_id'] ) {
          $this->getToDb()->update('engine4_event_albums', array(
            'photo_id' => $file_id,
          ), array(
            'album_id = ?' => $data['eventmedia_eventalbum_id'],
          ));
        }

      }
    }

    // search
    if( @$data['eventalbum_search'] ) {
      $this->_insertSearch('event_photo', @$newData['photo_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_eventmedia` (
*  `eventmedia_id` int(10) unsigned NOT NULL auto_increment,
*  `eventmedia_eventalbum_id` int(10) unsigned NOT NULL default '0',
*  `eventmedia_user_id` int(10) unsigned NOT NULL default '0',
*  `eventmedia_date` int(10) unsigned NOT NULL default '0',
*  `eventmedia_title` varchar(50) collate utf8_unicode_ci default NULL,
*  `eventmedia_desc` text collate utf8_unicode_ci,
-  `eventmedia_ext` varchar(8) collate utf8_unicode_ci default NULL,
-  `eventmedia_filesize` int(10) unsigned NOT NULL default '0',
*  `eventmedia_totalcomments` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventmedia_id`),
  KEY `INDEX` (`eventmedia_eventalbum_id`),
  KEY `USER` (`eventmedia_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_event_photos` (
*  `photo_id` int(11) unsigned NOT NULL auto_increment,
*  `album_id` int(11) unsigned NOT NULL,
*  `event_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,

*  `title` varchar(128) NOT NULL,
*  `description` varchar(255) NOT NULL,
*  `collection_id` int(11) unsigned NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`photo_id`),
  KEY (`album_id`),
  KEY (`event_id`),
  KEY (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */