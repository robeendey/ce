<?php

class Install_Import_Version3_ClassifiedAlbums extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_classifiedalbums';

  protected $_toTable = 'engine4_classified_albums';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['album_id'] = $data['classifiedalbum_id'];
    $newData['classified_id'] = $data['classifiedalbum_classified_id'];
    $newData['title'] = $data['classifiedalbum_title'];
    $newData['description'] = $data['classifiedalbum_desc'];
    $newData['creation_date'] = $this->_translateTime($data['classifiedalbum_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['classifiedalbum_dateupdated']);
    $newData['search'] = $data['classifiedalbum_search'];
    $newData['view_count'] = $data['classifiedalbum_views'];
    $newData['collectible_count'] = $data['classifiedalbum_totalfiles'];
    //$newData['photo_id'] = $data['classifiedalbum_cover'];

    // privacy
    // Note: don't need privacy, kinda defeats the purpose
    //$this->_insertPrivacy('classified_album', $data['classifiedalbum_id'], 'view', $this->_translatePrivacy($data['classifiedalbum_privacy'], 'parent'));
    //$this->_insertPrivacy('classified_album', $data['classifiedalbum_id'], 'comment', $this->_translatePrivacy($data['classifiedalbum_comments'], 'parent'));

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('classified_album', @$newData['album_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_classifiedalbums` (
*  `classifiedalbum_id` int(10) unsigned NOT NULL auto_increment,
*  `classifiedalbum_classified_id` int(10) unsigned NOT NULL default '0',
*  `classifiedalbum_datecreated` int(11) NOT NULL default '0',
*  `classifiedalbum_dateupdated` int(11) NOT NULL default '0',
*  `classifiedalbum_title` varchar(64) collate utf8_unicode_ci default NULL,
*  `classifiedalbum_desc` text collate utf8_unicode_ci,
*  `classifiedalbum_search` tinyint(3) unsigned NOT NULL default '0',
?  `classifiedalbum_privacy` tinyint(3) unsigned NOT NULL default '0',
?  `classifiedalbum_comments` tinyint(3) unsigned NOT NULL default '0',
*  `classifiedalbum_cover` int(10) unsigned NOT NULL default '0',
*  `classifiedalbum_views` int(10) unsigned NOT NULL default '0',
*  `classifiedalbum_totalfiles` smallint(5) unsigned NOT NULL default '0',
-  `classifiedalbum_totalspace` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedalbum_id`),
  KEY `INDEX` (`classifiedalbum_classified_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_albums` (
*  `album_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `classified_id` int(11) unsigned NOT NULL,
*  `title` varchar(128) NOT NULL,
*  `description` mediumtext NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `search` tinyint(1) NOT NULL default '1',
*  `photo_id` int(11) unsigned NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
-  `comment_count` int(11) unsigned NOT NULL default '0',
*  `collectible_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`album_id`),
  KEY `classified_id` (`classified_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */