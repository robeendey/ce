<?php

class Install_Import_Version3_GroupAlbums extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groupalbums';

  protected $_toTable = 'engine4_group_albums';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['album_id'] = $data['groupalbum_id'];
    $newData['group_id'] = $data['groupalbum_group_id'];
    $newData['title'] = $data['groupalbum_title'];
    $newData['description'] = $data['groupalbum_desc'];
    $newData['creation_date'] = $this->_translateTime($data['groupalbum_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['groupalbum_dateupdated']);
    $newData['search'] = $data['groupalbum_search'];
    $newData['view_count'] = $data['groupalbum_views'];
    $newData['collectible_count'] = $data['groupalbum_totalfiles'];
    //$newData['photo_id'] = $data['groupalbum_cover'];

    // privacy
    // Note: don't need privacy
    //$this->_insertPrivacy('group_album', $data['groupalbum_id'], 'view', $this->_translateGroupPrivacy($data['groupalbum_privacy'], 'parent'));
    //$this->_insertPrivacy('group_album', $data['groupalbum_id'], 'comment', $this->_translateGroupPrivacy($data['groupalbum_comments'], 'parent'));
    
    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('group_album', @$newData['album_id'], @$newData['title'], @$newData['description']);
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_groupalbums` (
*  `groupalbum_id` int(10) unsigned NOT NULL auto_increment,
*  `groupalbum_group_id` int(10) unsigned NOT NULL default '0',
*  `groupalbum_datecreated` int(11) NOT NULL default '0',
*  `groupalbum_dateupdated` int(11) NOT NULL default '0',
*  `groupalbum_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `groupalbum_desc` text collate utf8_unicode_ci,
*  `groupalbum_search` tinyint(1) unsigned NOT NULL default '0',
  `groupalbum_privacy` tinyint(3) unsigned NOT NULL default '0',
  `groupalbum_comments` tinyint(3) unsigned NOT NULL default '0',
*  `groupalbum_cover` int(10) unsigned NOT NULL default '0',
*  `groupalbum_views` int(10) unsigned NOT NULL default '0',
*  `groupalbum_totalfiles` smallint(5) unsigned NOT NULL default '0',
-  `groupalbum_totalspace` bigint(20) unsigned NOT NULL default '0',
  `groupalbum_tag` int(2) NOT NULL default '0',
  PRIMARY KEY  (`groupalbum_id`),
  KEY `INDEX` (`groupalbum_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_group_albums` (
*  `album_id` int(11) unsigned NOT NULL auto_increment,
*  `group_id` int(11) unsigned NOT NULL,
*  `title` varchar(128) NOT NULL,
*  `description` varchar(255) NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `search` tinyint(1) NOT NULL default '1',
*  `photo_id` int(11) unsigned NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
-  `comment_count` int(11) unsigned NOT NULL default '0',
*  `collectible_count` int(11) unsigned NOT NULL default '0',
   PRIMARY KEY (`album_id`),
   KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */