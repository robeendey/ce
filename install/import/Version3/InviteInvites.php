<?php

class Install_Import_Version3_InviteInvites extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_invites';

  protected $_toTable = 'engine4_invites';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    if( empty($data['invite_code']) || strlen($data['invite_code']) <= 4 ) {
      return false;
    }

    $newData['id'] = $data['invite_id'];
    $newData['user_id'] = $data['invite_user_id'];
    $newData['timestamp'] = $this->_translateTime($data['invite_date']);
    $newData['recipient'] = $data['invite_email'];
    $newData['code'] = $data['invite_code'];

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_invites` (
*  `invite_id` int(9) NOT NULL auto_increment,
*  `invite_user_id` int(9) NOT NULL default '0',
*  `invite_date` int(14) NOT NULL default '0',
*  `invite_email` varchar(70) collate utf8_unicode_ci NOT NULL default '',
*  `invite_code` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`invite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_invites` (
*  `id` int(10) unsigned NOT NULL auto_increment,
*  `user_id` int(11) unsigned NOT NULL,
*  `recipient` varchar(255) NOT NULL,
*  `code` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `timestamp` datetime NOT NULL,
  `message` text NOT NULL,
  `new_user_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `user_id` (`user_id`),
  KEY `recipient` (`recipient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */