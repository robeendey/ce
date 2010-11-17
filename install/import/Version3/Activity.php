<?php

class Install_Import_Version3_Activity extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;

  protected $_priority = 10000;

  protected function _run()
  {
    $this->_message('Not implemented', 2);
  }
  
  protected function _translateRow(array $data, $key = null)
  {
    return false;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_actionmedia` (
  `actionmedia_id` int(9) NOT NULL auto_increment,
  `actionmedia_action_id` int(9) NOT NULL,
  `actionmedia_path` varchar(250) collate utf8_unicode_ci NOT NULL,
  `actionmedia_link` varchar(250) collate utf8_unicode_ci NOT NULL,
  `actionmedia_width` int(3) NOT NULL,
  `actionmedia_height` int(3) NOT NULL,
  PRIMARY KEY  (`actionmedia_id`),
  KEY `actionmedia_action_id` (`actionmedia_action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_activity_attachments` (
  `attachment_id` int(11) unsigned NOT NULL auto_increment,
  `action_id` int(11) unsigned NOT NULL,
  `type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `id` int(11) unsigned NOT NULL,
  `mode` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`attachment_id`),
  KEY `action_id` (`action_id`),
  KEY `type_id` (`type`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */





/*
CREATE TABLE IF NOT EXISTS `se_actions` (
  `action_id` int(9) NOT NULL auto_increment,
  `action_actiontype_id` int(9) NOT NULL default '0',
  `action_date` int(14) NOT NULL default '0',
  `action_user_id` int(9) NOT NULL default '0',
  `action_text` text collate utf8_unicode_ci NOT NULL,
  `action_object_owner` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `action_object_owner_id` int(9) NOT NULL default '0',
  `action_object_privacy` int(2) NOT NULL default '0',
  PRIMARY KEY  (`action_id`),
  KEY `action_user_id` (`action_user_id`),
  KEY `action_date` (`action_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=71 ;

INSERT INTO `se_actions` (`action_id`, `action_actiontype_id`, `action_date`, `action_user_id`, `action_text`, `action_object_owner`, `action_object_owner_id`, `action_object_privacy`) VALUES
(67, 4, 1259978005, 1, 'a:8:{i:0;s:9:"JohnBoehr";i:1;s:10:"John Boehr";i:2;s:9:"JohnBoehr";i:3;s:10:"John Boehr";i:4;s:10:"teetetetet";i:5;s:1:"1";i:6;s:8:"Untitled";i:7;s:1:"1";}', 'user', 1, 63),
 * 
 */

/*
CREATE TABLE `engine4_activity_actions` (
  `action_id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(32) NOT NULL,
  `subject_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `object_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `body` text NULL,
  `params` text NULL,
  `date` datetime NOT NULL,
  `attachment_count` smallint(3) unsigned NOT NULL default '0',
  `comment_count` mediumint(5) unsigned NOT NULL default '0',
  `like_count` mediumint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`action_id`),
  KEY `SUBJECT` (`subject_type`,`subject_id`),
  KEY `OBJECT` (`object_type`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

/*
CREATE TABLE `engine4_activity_stream` (
  `target_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `target_id` int(11) unsigned NOT NULL,
  `subject_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `object_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `object_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `action_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`target_type`,`target_id`,`action_id`),
  KEY `SUBJECT` (`subject_type`,`subject_id`,`action_id`),
  KEY `OBJECT` (`object_type`,`object_id`,`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */





/*
CREATE TABLE IF NOT EXISTS `se_actiontypes` (
  `actiontype_id` int(9) NOT NULL auto_increment,
  `actiontype_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_icon` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_setting` int(1) NOT NULL default '0',
  `actiontype_enabled` int(1) NOT NULL default '0',
  `actiontype_desc` int(9) NOT NULL default '0',
  `actiontype_text` int(9) NOT NULL default '0',
  `actiontype_vars` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `actiontype_media` int(1) NOT NULL,
  PRIMARY KEY  (`actiontype_id`),
  UNIQUE KEY `actiontype_name` (`actiontype_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

INSERT INTO `se_actiontypes` (`actiontype_id`, `actiontype_name`, `actiontype_icon`, `actiontype_setting`, `actiontype_enabled`, `actiontype_desc`, `actiontype_text`, `actiontype_vars`, `actiontype_media`) VALUES
(1, 'login', 'action_login.gif', 1, 0, 700008, 700001, '[username],[displayname]', 0),
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_activity_actiontypes` (
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `module` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `body` text NOT NULL,
  `enabled` tinyint(1) NOT NULL default '1',
  `displayable` tinyint(1) NOT NULL default '3',
  `attachable` tinyint(1) NOT NULL default '1',
  `commentable` tinyint(1) NOT NULL default '1',
  `shareable` tinyint(1) NOT NULL default '1',
  `is_generated` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */









/*
CREATE TABLE IF NOT EXISTS `engine4_activity_actionsettings` (
  `user_id` int(11) unsigned NOT NULL,
  `type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `publish` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`user_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_activity_comments` (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `resource_type` (`resource_id`),
  KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
 * 
 */

/*
CREATE TABLE `engine4_activity_likes` (
  `like_id` int(11) unsigned NOT NULL auto_increment,
  `resource_id` int(11) unsigned NOT NULL,
  `poster_type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `poster_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`like_id`),
  KEY `resource_id` (`resource_id`),
  KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */
