<?php

class Install_Import_Version3_GroupGroups extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groups';

  protected $_toTable = 'engine4_group_groups';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['group_id'] = $data['group_id'];
    $newData['user_id'] = $data['group_user_id'];
    $newData['title'] = $data['group_title'];
    $newData['description'] = $data['group_desc'];
    $newData['search'] = $data['group_search'];
    $newData['invite'] = $data['group_invite'];
    $newData['approval'] = $data['group_approval'];
    $newData['creation_date'] = $this->_translateTime($data['group_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['group_dateupdated']);
    $newData['member_count'] = $data['group_totalmembers'];
    $newData['view_count'] = $data['group_views'];

    // privacy
    try {
      $this->_insertPrivacy('group', $data['group_id'], 'view', $this->_translateGroupPrivacy($data['group_privacy']));
      $this->_insertPrivacy('group', $data['group_id'], 'comment', $this->_translateGroupPrivacy($data['group_comments']));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['group_id'] . ' : ' . $e->getMessage());
    }

    // get photo
    if( !empty($data['group_photo']) ) {
      $file = $this->_getFromUserDir(
        $data['group_id'],
        'uploads_group',
        $data['group_photo']
      );
      if( file_exists($file) ) {
        try {
          if( $this->getParam('resizePhotos', true) ) {
            $file_id = $this->_translatePhoto($file, array(
              'parent_type' => 'group',
              'parent_id' => $data['group_id'],
              'user_id' => @$data['group_user_id'],
            ));
          } else {
            $file_id = $this->_translateFile($file, array(
              'parent_type' => 'group',
              'parent_id' => $data['group_id'],
              'user_id' => @$data['group_user_id'],
            ), true);
          }
        } catch( Exception $e ) {
          $this->_warning($e->getMessage(), 1);
          $file_id = null;
        }

        if( $file_id ) {
          $newData['photo_id'] = $file_id;
        }
      }
    }

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('group', @$newData['group_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_groups` (
*  `group_id` int(10) unsigned NOT NULL auto_increment,
*  `group_user_id` int(10) unsigned NOT NULL default '0',
  `group_groupcat_id` int(10) unsigned NOT NULL default '0',
*  `group_datecreated` int(11) NOT NULL default '0',
*  `group_dateupdated` int(11) NOT NULL default '0',
*  `group_views` int(10) unsigned NOT NULL default '0',
*  `group_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
*  `group_desc` text collate utf8_unicode_ci,
  `group_photo` varchar(16) collate utf8_unicode_ci NOT NULL default '',
*  `group_search` tinyint(1) unsigned NOT NULL default '0',
  `group_privacy` tinyint(3) unsigned NOT NULL default '0',
  `group_comments` tinyint(3) unsigned NOT NULL default '0',
*  `group_approval` tinyint(1) unsigned NOT NULL default '0',
  `group_totalcomments` smallint(5) unsigned NOT NULL default '0',
*  `group_totalmembers` smallint(5) unsigned NOT NULL default '0',
  `group_totaltopics` smallint(5) unsigned NOT NULL default '0',
  `group_discussion` int(2) NOT NULL default '0',
*  `group_invite` int(1) NOT NULL default '0',
  `group_upload` int(1) NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `INDEX` (`group_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_groups` (
*  `group_id` int(11) unsigned NOT NULL auto_increment,
*  `user_id` int(11) unsigned NOT NULL,

*  `title` varchar(64) NOT NULL,
*  `description` text NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
*  `search` tinyint(1) NOT NULL default '1',
*  `invite` tinyint(1) NOT NULL default '1',
*  `approval` tinyint(1) NOT NULL default '0',
  `photo_id` int(11) unsigned NOT NULL default '0',
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `member_count` smallint(6) unsigned NOT NULL,
*  `view_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  KEY `user_id` (`user_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */











/*
CREATE TABLE IF NOT EXISTS `se_groupstyles` (
  `groupstyle_id` int(9) NOT NULL auto_increment,
  `groupstyle_group_id` int(9) NOT NULL default '0',
  `groupstyle_css` text collate utf8_unicode_ci,
  PRIMARY KEY  (`groupstyle_id`),
  KEY `INDEX` (`groupstyle_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_groupsubscribes` (
  `groupsubscribe_user_id` int(9) NOT NULL default '0',
  `groupsubscribe_group_id` int(9) NOT NULL default '0',
  `groupsubscribe_time` int(14) NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`groupsubscribe_user_id`,`groupsubscribe_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */
















/*
CREATE TABLE IF NOT EXISTS `se_groupfields` (
  `groupfield_id` int(9) NOT NULL auto_increment,
  `groupfield_order` int(3) NOT NULL default '0',
  `groupfield_dependency` int(9) NOT NULL default '0',
  `groupfield_type` int(1) NOT NULL default '0',
  `groupfield_style` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `groupfield_maxlength` int(3) NOT NULL default '0',
  `groupfield_options` longtext collate utf8_unicode_ci,
  `groupfield_required` int(1) NOT NULL default '0',
  `groupfield_regex` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `groupfield_title` int(10) unsigned NOT NULL default '0',
  `groupfield_desc` int(10) unsigned NOT NULL default '0',
  `groupfield_error` int(10) unsigned NOT NULL default '0',
  `groupfield_groupcat_id` int(9) NOT NULL default '0',
  `groupfield_signup` tinyint(3) unsigned NOT NULL default '0',
  `groupfield_link` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `groupfield_display` tinyint(3) unsigned NOT NULL default '0',
  `groupfield_special` tinyint(3) unsigned NOT NULL default '0',
  `groupfield_html` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `groupfield_search` int(1) NOT NULL default '0',
  PRIMARY KEY  (`groupfield_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_groupvalues` (
  `groupvalue_id` int(9) NOT NULL auto_increment,
  `groupvalue_group_id` int(9) NOT NULL default '0',
  PRIMARY KEY  (`groupvalue_id`),
  KEY `groupvalue_group_id` (`groupvalue_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */














/*
CREATE TABLE IF NOT EXISTS `engine4_group_listitems` (
  `listitem_id` int(11) unsigned NOT NULL auto_increment,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`listitem_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_lists` (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(64) NOT NULL default '',
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */