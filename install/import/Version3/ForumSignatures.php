<?php

class Install_Import_Version3_ForumSignatures extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumusers';

  protected $_toTable = 'engine4_forum_signatures';

  protected $_priority = 90;
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['user_id'] = $data['forumuser_user_id'];
    $newData['post_count'] = $data['forumuser_totalposts'];
    //$newData['creation_date'] = '0000-00-00 00:00';
    //$newData['modified_date'] = '0000-00-00 00:00';

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forumusers` (
*  `forumuser_user_id` int(9) NOT NULL default '0',
*  `forumuser_totalposts` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forumuser_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_signatures` (
  `signature_id` int(11) unsigned NOT NULL auto_increment,
*  `user_id` int(11) unsigned NOT NULL,
*  `body` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
*  `post_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`signature_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 *
 */