<?php

class Install_Import_Version3_EventAlbums extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_eventalbums';

  protected $_toTable = 'engine4_event_albums';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['album_id'] = $data['eventalbum_id'];
    $newData['event_id'] = $data['eventalbum_event_id'];
    $newData['title'] = $data['eventalbum_title'];
    $newData['description'] = $data['eventalbum_desc'];
    $newData['creation_date'] = $this->_translateTime($data['eventalbum_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['eventalbum_dateupdated']);
    $newData['search'] = $data['eventalbum_search'];
    $newData['view_count'] = $data['eventalbum_views'];
    $newData['collectible_count'] = $data['eventalbum_totalfiles'];
    //$newData['photo_id'] = $data['eventalbum_cover'];

    // privacy
    // Note: don't need privacy
    //$this->_insertPrivacy('event_album', $data['eventalbum_id'], 'view', $this->_translateEventPrivacy($data['eventalbum_privacy'], 'parent'));
    //$this->_insertPrivacy('event_album', $data['eventalbum_id'], 'comment', $this->_translateEventPrivacy($data['eventalbum_comments'], 'parent'));

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('event_album', @$newData['album_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_eventalbums` (
*  `eventalbum_id` int(10) unsigned NOT NULL auto_increment,
*  `eventalbum_event_id` int(10) unsigned NOT NULL default '0',
*  `eventalbum_datecreated` int(10) unsigned NOT NULL default '0',
*  `eventalbum_dateupdated` int(10) unsigned NOT NULL default '0',
*  `eventalbum_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `eventalbum_desc` text collate utf8_unicode_ci,
*  `eventalbum_search` tinyint(3) unsigned NOT NULL default '0',
  `eventalbum_privacy` tinyint(3) unsigned NOT NULL default '0',
  `eventalbum_comments` tinyint(3) unsigned NOT NULL default '0',
*  `eventalbum_cover` int(10) unsigned NOT NULL default '0',
*  `eventalbum_views` int(10) unsigned NOT NULL default '0',
  `eventalbum_tag` tinyint(3) unsigned NOT NULL default '0',
*  `eventalbum_totalfiles` smallint(5) unsigned NOT NULL default '0',
  `eventalbum_totalspace` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventalbum_id`),
  KEY `INDEX` (`eventalbum_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_event_albums` (
*  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `event_id` int(11) unsigned NOT NULL,
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
   KEY (`event_id`),
   KEY (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */