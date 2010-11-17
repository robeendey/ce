<?php

class Install_Import_Version3_GroupTopics extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_grouptopics';

  protected $_toTable = 'engine4_group_topics';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['topic_id'] = $data['grouptopic_id'];
    $newData['group_id'] = $data['grouptopic_group_id'];
    $newData['user_id'] = $data['grouptopic_creatoruser_id'];
    $newData['title'] = $data['grouptopic_subject'];
    $newData['creation_date'] = $this->_translateTime($data['grouptopic_date']);
    $newData['modified_date'] = $this->_translateTime($data['grouptopic_date']);
    $newData['sticky'] = $data['grouptopic_sticky'];
    $newData['closed'] = $data['grouptopic_closed'];
    $newData['view_count'] = $data['grouptopic_views'];
    $newData['post_count'] = $data['grouptopic_totalposts'];

    // Lookup the last poster
    $lastPostInfo = $this->getFromDb()->select()
      ->from('se_groupposts', array('grouppost_id', 'grouppost_date', 'grouppost_authoruser_id'))
      ->where('grouppost_grouptopic_id = ?', $data['grouptopic_id'])
      ->order('grouppost_id DESC')
      ->limit(1)
      ->query()
      ->fetch()
      ;

    if( !empty($lastPostInfo) ) {
      $newData['lastpost_id'] = $lastPostInfo['grouppost_id'];
      $newData['lastposter_id'] = $lastPostInfo['grouppost_authoruser_id'];
    }
    
    // search
    //if( @$newData['search'] ) {
    //  $this->_insertSearch('group_topic', @$newData['topic_id'], @$newData['title'], @$newData['description']);
    //}

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_grouptopics` (
*  `grouptopic_id` int(9) NOT NULL auto_increment,
*  `grouptopic_group_id` int(9) NOT NULL default '0',
*  `grouptopic_creatoruser_id` int(9) NOT NULL default '0',
*  `grouptopic_date` int(14) NOT NULL default '0',
*  `grouptopic_subject` varchar(50) collate utf8_unicode_ci NOT NULL default '',
*  `grouptopic_views` int(9) NOT NULL default '0',
*  `grouptopic_sticky` tinyint(1) unsigned NOT NULL default '0',
*  `grouptopic_closed` tinyint(1) unsigned NOT NULL default '0',
*  `grouptopic_totalposts` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`grouptopic_id`),
  KEY `INDEX` (`grouptopic_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_topics` (
*  `topic_id` int(11) unsigned NOT NULL auto_increment,
*  `group_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,

*  `title` varchar(64) NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `sticky` tinyint(1) NOT NULL default '0',
*  `closed` tinyint(1) NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `post_count` int(11) unsigned NOT NULL default '0',
*  `lastpost_id` int(11) unsigned NOT NULL,
*  `lastposter_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`topic_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */