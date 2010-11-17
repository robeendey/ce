<?php

class Install_Import_Version3_ForumCategories extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumcats';

  protected $_toTable = 'engine4_forum_categories';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    // get forum count
    $forumCount = count($this->getFromDb()->select()
      ->from('se_forums', 'forum_id')
      ->where('forum_forumcat_id = ?', $data['forumcat_id'])
      ->query()
      ->fetchAll());

    
    $newData['category_id'] = $data['forumcat_id'];
    $newData['title'] = (string) $this->_getLanguageValue($data['forumcat_title']);
    //$newData['creation_date'] = '0000-00-00 00:00';
    //$newData['modified_date'] = '0000-00-00 00:00';
    $newData['order'] = $data['forumcat_order'];
    $newData['forum_count'] = $forumCount;

    // search
    //if( @$newData['search'] ) {
      $this->_insertSearch('forum_category', @$newData['category_id'], @$newData['title'], @$newData['description']);
    //}
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forumcats` (
*  `forumcat_id` int(9) NOT NULL auto_increment,
*  `forumcat_order` smallint(5) unsigned NOT NULL default '0',
*  `forumcat_title` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forumcat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_categories` (
*  `category_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(64) NOT NULL,
-  `description` varchar(255) NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
*  `order` smallint(6) NOT NULL default '0',
*  `forum_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 *
 */