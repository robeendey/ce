<?php

class Install_Import_Version3_UserMembership extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_friends';

  protected $_toTable = 'engine4_user_membership';
  
  protected function _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['resource_id'] = $data['friend_user_id1'];
    $newData['user_id'] = $data['friend_user_id2'];

    $newData['active'] = $data['friend_status'];

    if( $data['friend_status'] ) {
      $newData['resource_approved'] = true;
      $newData['user_approved'] = true;
    } else {
      // @todo verify correct
      $newData['resource_approved'] = 1;
      $newData['user_approved'] = 0;
    }

    return $newData;
  }

}

/*
CREATE TABLE IF NOT EXISTS `se_friends` (
  `friend_id` int(9) NOT NULL auto_increment,
  `friend_user_id1` int(9) NOT NULL default '0',
  `friend_user_id2` int(9) NOT NULL default '0',
  `friend_status` int(1) NOT NULL default '0',
  `friend_type` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`friend_id`),
  UNIQUE KEY `friend_user_id` (`friend_user_id1`,`friend_user_id2`),
  KEY `friend_status` (`friend_status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_user_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text default NULL,
  `description` text default NULL,
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */







/*
CREATE TABLE IF NOT EXISTS `se_friendexplains` (
  `friendexplain_id` int(9) NOT NULL auto_increment,
  `friendexplain_friend_id` int(9) NOT NULL default '0',
  `friendexplain_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`friendexplain_id`),
  KEY `friend_id` (`friendexplain_friend_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */
