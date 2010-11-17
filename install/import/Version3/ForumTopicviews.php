<?php

class Install_Import_Version3_ForumTopicviews extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumlogs';

  protected $_toTable = 'engine4_forum_topicviews';

  protected function _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['user_id'] = $data['forumlog_user_id'];
    $newData['topic_id'] = $data['forumlog_forumtopic_id'];
    $newData['last_view_date'] = $this->_translateTime($data['forumlog_date']);

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forumlogs` (
*  `forumlog_user_id` int(9) NOT NULL default '0',
*  `forumlog_forumtopic_id` int(9) NOT NULL default '0',
*  `forumlog_date` int(14) NOT NULL default '0',
  UNIQUE KEY `unique` (`forumlog_user_id`,`forumlog_forumtopic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_topicviews` (
*  `user_id` int(11) unsigned NOT NULL,
*  `topic_id` int(11) unsigned NOT NULL,
*  `last_view_date` datetime NOT NULL,
  PRIMARY KEY(`user_id`, `topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */