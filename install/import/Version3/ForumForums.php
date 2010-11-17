<?php

class Install_Import_Version3_ForumForums extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forums';

  protected $_toTable = 'engine4_forum_forums';

  protected $_priority = 80;

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['forum_id'] = $data['forum_id'];
    $newData['category_id'] = $data['forum_forumcat_id'];
    $newData['title'] = (string) $this->_getLanguageValue($data['forum_title']);
    $newData['description'] = (string) $this->_getLanguageValue($data['forum_desc']);
    //$newData['creation_date'] = '0000-00-00 00:00';
    //$newData['modified_date'] = '0000-00-00 00:00';
    $newData['order'] = $data['forum_order'];
    $newData['topic_count'] = $data['forum_totaltopics'];
    // no workie $newData['post_count'] = $data['forum_totalreplies'] + 1;

    // Get last post
    $lastPostData = $this->getToDb()->select()
      ->from('engine4_forum_posts', array('post_id', 'user_id', 'creation_date'))
      ->where('forum_id = ?', $data['forum_id'])
      ->limit(1)
      ->order('creation_date DESC')
      ->query()
      ->fetch()
      ;

    if( !empty($lastPostData) ) {
      $newData['lastpost_id'] = $lastPostData['post_id'];
      $newData['lastposter_id'] = $lastPostData['user_id'];
      $newData['modified_date'] = $lastPostData['creation_date'];
    }

    // get post count
    $postCount = $this->getToDb()->select()
      ->from('engine4_forum_posts', new Zend_Db_Expr('COUNT(*)'))
      ->where('forum_id = ?', $data['forum_id'])
      ->query()
      ->fetchColumn(0)
      ;

    $newData['post_count'] = $postCount;

    // search
    //if( @$newData['search'] ) {
      $this->_insertSearch('forum', @$newData['forum_id'], @$newData['title'], @$newData['description']);
    //}

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forums` (
*  `forum_id` int(10) unsigned NOT NULL auto_increment,
*  `forum_forumcat_id` int(10) unsigned NOT NULL default '0',
*  `forum_order` smallint(5) unsigned NOT NULL default '0',
*  `forum_title` int(10) unsigned NOT NULL default '0',
*  `forum_desc` int(10) unsigned NOT NULL default '0',
*  `forum_totaltopics` smallint(5) unsigned NOT NULL default '0',
*  `forum_totalreplies` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`),
  KEY `INDEX` (`forum_forumcat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_forums` (
*  `forum_id` int(11) unsigned NOT NULL auto_increment,
*  `category_id` int(11) unsigned NOT NULL,
*  `title` varchar(64) NOT NULL,
*  `description` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
*  `order` smallint(6) NOT NULL default '999',
  `file_id` int(11) unsigned NOT NULL default '0',
*  `topic_count` int(11) unsigned NOT NULL default '0',
*  `post_count` int(11) unsigned NOT NULL default '0',
  `lastpost_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forum_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 *
 */
