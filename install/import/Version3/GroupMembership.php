<?php

class Install_Import_Version3_GroupMembership extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groupmembers';

  protected $_toTable = 'engine4_group_membership';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['resource_id'] = $data['groupmember_group_id'];
    $newData['user_id'] = $data['groupmember_user_id'];
    $newData['active'] = $data['groupmember_status'] && $data['groupmember_approved'];
    $newData['resource_approved'] = $data['groupmember_approved'];
    $newData['user_approved'] = $data['groupmember_status'];
    $newData['title'] = $data['groupmember_title'];

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_groupmembers` (
  `groupmember_id` int(9) NOT NULL auto_increment,
*  `groupmember_user_id` int(9) NOT NULL default '0',
*  `groupmember_group_id` int(9) NOT NULL default '0',
*  `groupmember_status` int(1) NOT NULL default '0',
*  `groupmember_approved` int(1) NOT NULL default '0',
  `groupmember_rank` int(1) NOT NULL default '0',
*  `groupmember_title` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`groupmember_id`),
  KEY `INDEX` (`groupmember_user_id`,`groupmember_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_membership` (
*  `resource_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `active` tinyint(1) NOT NULL default '0',
*  `resource_approved` tinyint(1) NOT NULL default '0',
*  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text NULL,
*  `title` text NULL,
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */