<?php

class Install_Import_Version3_ForumMembership extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumlevels';

  protected $_toTable = 'engine4_forum_membership';

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
CREATE TABLE IF NOT EXISTS `se_forumlevels` (
  `forumlevel_forum_id` int(10) unsigned NOT NULL default '0',
  `forumlevel_level_id` int(10) unsigned NOT NULL default '0',
  `forumlevel_post` tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY `unique` (`forumlevel_forum_id`,`forumlevel_level_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */



/*
CREATE TABLE IF NOT EXISTS `engine4_forum_membership` (
  `resource_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default '0',
  `resource_approved` tinyint(1) NOT NULL default '0',
  `moderator` tinyint(1) NOT NULL default '0',
  PRIMARY KEY(`resource_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */