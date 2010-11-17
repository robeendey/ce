<?php

class Install_Import_Version3_GroupPhotos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groupmedia';

  protected $_fromJoinTable = 'se_groupalbums';

  protected $_fromJoinCondition = 'groupalbum_id=groupmedia_groupalbum_id';

  protected $_toTable = 'engine4_group_photos';

  protected $_priority = 90;

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['photo_id'] = $data['groupmedia_id'];
    $newData['album_id'] = $data['groupmedia_groupalbum_id'];
    $newData['group_id'] = @$data['groupalbum_group_id'];
    $newData['user_id'] = $data['groupmedia_user_id'];
    $newData['title'] = (string) @$data['groupmedia_title'];
    $newData['description'] = (string) @$data['groupmedia_desc'];
    $newData['collection_id'] = $data['groupmedia_groupalbum_id'];
    $newData['creation_date'] = $this->_translateTime($data['groupmedia_date']);
    $newData['modified_date'] = $this->_translateTime($data['groupmedia_date']);
    $newData['comment_count'] = $data['groupmedia_totalcomments'];

    // Import file
    $file = $this->_getFromUserDir(
      @$data['groupalbum_group_id'],
      'uploads_group',
      $data['groupmedia_id'] . '.' . $data['groupmedia_ext']
    );

    if( file_exists($file) ) {
      try {
        if( $this->getParam('resizePhotos', true) ) {
          $file_id = $this->_translatePhoto($file, array(
            'parent_type' => 'group_photo',
            'parent_id' => $data['groupmedia_id'],
            'user_id' => $data['groupmedia_user_id'],
          ));
        } else {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'group_photo',
            'parent_id' => $data['groupmedia_id'],
            'user_id' => $data['groupmedia_user_id'],
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
        $coverPhotoId = @$data['groupalbum_cover'];

        if( $coverPhotoId && $coverPhotoId == $data['groupmedia_id'] ) {
          $this->getToDb()->update('engine4_group_albums', array(
            'photo_id' => $file_id,
          ), array(
            'album_id = ?' => $data['groupmedia_groupalbum_id'],
          ));
        }

      }
    }

    // search
    if( @$data['groupalbum_search'] ) {
      $this->_insertSearch('group_photo', @$newData['photo_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_groupmedia` (
*  `groupmedia_id` int(10) unsigned NOT NULL auto_increment,
*  `groupmedia_groupalbum_id` int(10) unsigned NOT NULL default '0',
*  `groupmedia_date` int(11) NOT NULL default '0',
*  `groupmedia_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `groupmedia_desc` text collate utf8_unicode_ci,
-  `groupmedia_ext` varchar(8) collate utf8_unicode_ci NOT NULL default '',
-  `groupmedia_filesize` int(10) unsigned NOT NULL default '0',
*  `groupmedia_totalcomments` smallint(5) unsigned NOT NULL default '0',
*  `groupmedia_user_id` int(9) NOT NULL default '0',
  PRIMARY KEY  (`groupmedia_id`),
  KEY `INDEX` (`groupmedia_groupalbum_id`),
  KEY `groupmedia_user_id` (`groupmedia_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_group_photos` (
*  `photo_id` int(11) unsigned NOT NULL auto_increment,
*  `album_id` int(11) unsigned NOT NULL,
*  `group_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,

*  `title` varchar(128) NOT NULL,
*  `description` varchar(255) NOT NULL,
*  `collection_id` int(11) unsigned NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
-  `view_count` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`photo_id`),
  KEY `album_id` (`album_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */