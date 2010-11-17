<?php

class Install_Import_Version3_ForumTopics extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumtopics';

  protected $_toTable = 'engine4_forum_topics';

  protected $_priority = 90;

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['topic_id'] = $data['forumtopic_id'];
    $newData['forum_id'] = $data['forumtopic_forum_id'];
    $newData['user_id'] = $data['forumtopic_creatoruser_id'];
    $newData['title'] = $data['forumtopic_subject'];
    $newData['description'] = $data['forumtopic_excerpt'];
    $newData['creation_date'] = $this->_translateTime($data['forumtopic_date']);
    $newData['modified_date'] = $this->_translateTime($data['forumtopic_date']);
    $newData['sticky'] = $data['forumtopic_sticky'];
    $newData['closed'] = $data['forumtopic_closed'];
    $newData['post_count'] = $data['forumtopic_totalreplies'] + 1;
    $newData['view_count'] = $data['forumtopic_views'];

    // get lastpost/lastposter
    $lastPostData = $this->getFromDb()->select()
      ->from('se_forumposts', array('forumpost_authoruser_id', 'forumpost_id'))
      ->where('forumpost_forumtopic_id = ?', $data['forumtopic_id'])
      ->order('forumpost_date DESC')
      ->limit(1)
      ->query()
      ->fetch()
      ;

    if( !empty($lastPostData) ) {
      $newData['lastpost_id'] = $lastPostData['forumpost_id'];
      $newData['lastposter_id'] = $lastPostData['forumpost_authoruser_id'];
    }
    
    // search
    //if( @$newData['search'] ) {
      $this->_insertSearch('forum_topic', @$newData['topic_id'], @$newData['title'], @$newData['description']);
    //}

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forumtopics` (
*  `forumtopic_id` int(9) NOT NULL auto_increment,
*  `forumtopic_forum_id` int(9) NOT NULL default '0',
*  `forumtopic_creatoruser_id` int(9) NOT NULL default '0',
*  `forumtopic_date` int(14) NOT NULL default '0',
*  `forumtopic_subject` varchar(50) collate utf8_unicode_ci NOT NULL default '',
*  `forumtopic_excerpt` varchar(100) collate utf8_unicode_ci NOT NULL default '',
*  `forumtopic_views` int(9) NOT NULL default '0',
*  `forumtopic_sticky` tinyint(1) unsigned NOT NULL default '0',
*  `forumtopic_closed` tinyint(1) unsigned NOT NULL default '0',
*  `forumtopic_totalreplies` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forumtopic_id`),
  KEY `INDEX` (`forumtopic_forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_topics` (
*  `topic_id` int(11) unsigned NOT NULL auto_increment,
*  `forum_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `title` varchar(64) NOT NULL,
*  `description` varchar(255) NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `sticky` tinyint(4) NOT NULL default '0',
*  `closed` tinyint(4) NOT NULL default '0',
*  `post_count` int(11) unsigned NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `lastpost_id` int(11) unsigned NOT NULL default '0',
*  `lastposter_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`topic_id`),
  KEY `forum_id` (`forum_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */