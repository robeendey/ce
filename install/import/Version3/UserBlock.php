<?php

class Install_Import_Version3_UserBlock extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_users';

  protected $_toTable = 'engine4_user_block';
  
  protected function  _translateRow(array $data, $key = null)
  {
    // Get blocked users
    $blocked = $this->_translateCommaStringToArray($data['user_blocklist']);
    $blocked = array_filter($blocked, 'is_numeric');
    
    if( empty($blocked) || !is_array($blocked) ) {
      return false;
    }

    // Custom
    foreach( $blocked as $blockedUserId ) {
      $this->getToDb()->insert($this->getToTable(), array(
        'user_id' => $data['user_id'],
        'blocked_user_id' => $blockedUserId
      ));
    }

    // Cancel standard translation
    return false;
  }
}

/*
*  `user_blocklist` text collate utf8_unicode_ci,
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_user_block` (
*  `user_id` int(11) unsigned NOT NULL,
*  `blocked_user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`blocked_user_id`),
  KEY `REVERSE` (`blocked_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */