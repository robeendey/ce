<?php

class Install_Import_Version3_AlbumAlbums extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_albums';

  protected $_toTable = 'engine4_album_albums';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['album_id'] = $data['album_id'];
    $newData['title'] = $data['album_title'];
    $newData['description'] = $data['album_desc'];
    $newData['owner_type'] = 'user';
    $newData['owner_id'] = $data['album_user_id'];
    $newData['creation_date'] = $this->_translateTime($data['album_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['album_dateupdated']);
    $newData['view_count'] = $data['album_views'];
    $newData['search'] = $data['album_search'];
    //$newData['photo_id'] = $data['album_cover'];

    // privacy
    $this->_insertPrivacy('album', $data['album_id'], 'view', $this->_translatePrivacy($data['album_privacy'], 'owner'));
    $this->_insertPrivacy('album', $data['album_id'], 'comment', $this->_translatePrivacy($data['album_comments'], 'owner'));

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('album', @$newData['album_id'], @$newData['title'], @$newData['description']);
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_albums` (
*  `album_id` int(10) unsigned NOT NULL auto_increment,
*  `album_user_id` int(10) unsigned NOT NULL default '0',
*  `album_datecreated` int(14) NOT NULL default '0',
*  `album_dateupdated` int(14) NOT NULL default '0',
*  `album_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `album_desc` text collate utf8_unicode_ci,
*  `album_search` tinyint(1) unsigned NOT NULL default '0',
*  `album_privacy` tinyint(2) unsigned NOT NULL default '0',
*  `album_comments` tinyint(2) unsigned NOT NULL default '0',
  `album_cover` int(11) NOT NULL default '0',
*  `album_views` int(10) unsigned NOT NULL default '0',
-  `album_totalfiles` smallint(5) unsigned NOT NULL default '0',
-  `album_totalspace` bigint(20) unsigned NOT NULL default '0',
  `album_order` int(1) NOT NULL default '0',
  `album_tag` int(2) NOT NULL default '0',
  PRIMARY KEY  (`album_id`),
  KEY `INDEX` (`album_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE `engine4_album_albums` (
*  `album_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(128) NOT NULL,
*  `description` mediumtext NOT NULL,
*  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `owner_id` int(11) unsigned NOT NULL,
-  `category_id` int(11) unsigned NOT NULL default '0',
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
-  `comment_count` int(11) unsigned NOT NULL default '0',
*  `search` tinyint(1) NOT NULL default '1',
-  `type` enum('wall','profile','message') NULL,
  PRIMARY KEY (`album_id`),
  KEY `owner_type` (`owner_type`, `owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */








/*
CREATE TABLE IF NOT EXISTS `se_albumstyles` (
  `albumstyle_id` int(9) NOT NULL auto_increment,
  `albumstyle_user_id` int(9) NOT NULL default '0',
  `albumstyle_css` text collate utf8_unicode_ci,
  PRIMARY KEY  (`albumstyle_id`),
  KEY `INDEX` (`albumstyle_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */










/*
CREATE TABLE IF NOT EXISTS `se_mediatags` (
  `mediatag_id` int(10) unsigned NOT NULL auto_increment,
  `mediatag_media_id` int(10) unsigned NOT NULL default '0',
  `mediatag_user_id` int(10) unsigned NOT NULL default '0',
  `mediatag_x` int(11) NOT NULL default '0',
  `mediatag_y` int(11) NOT NULL default '0',
  `mediatag_height` smallint(5) unsigned NOT NULL default '0',
  `mediatag_width` smallint(5) unsigned NOT NULL default '0',
  `mediatag_text` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `mediatag_date` int(14) NOT NULL default '0',
  PRIMARY KEY  (`mediatag_id`),
  KEY `INDEX` (`mediatag_media_id`,`mediatag_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */