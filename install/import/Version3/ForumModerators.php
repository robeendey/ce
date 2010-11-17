<?php

class Install_Import_Version3_ForumModerators extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forummoderators';

  protected $_toTable = 'engine4_forum_listitems';

  protected function _initPost()
  {
    if( $this->_toTableTruncate ) {
      try {
        $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier('engine4_forum_lists'));
      } catch( Exception $e ) {
        
      }
    }
  }

  protected function  _translateRow(array $data, $key = null)
  {
    // Check for list
    $forumListIdentity = $this->getToDb()->select()
      ->from('engine4_forum_lists', 'list_id')
      ->where('owner_id = ?', $data['forummoderator_forum_id'])
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    if( !$forumListIdentity ) {
      $this->getToDb()->insert('engine4_forum_lists', array(
        'owner_id' => $data['forummoderator_forum_id'],
        'child_count' => 0,
      ));
      $forumListIdentity = $this->getToDb()->lastInsertId();
    }

    // Make data
    $newData = array();

    $newData['list_id'] = $forumListIdentity;
    $newData['child_id'] = $data['forummoderator_user_id'];

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forummoderators` (
  `forummoderator_forum_id` int(10) unsigned NOT NULL default '0',
  `forummoderator_user_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `unique` (`forummoderator_forum_id`,`forummoderator_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_listitems` (
  `listitem_id` int(11) unsigned NOT NULL auto_increment,
  `list_id` int(11) unsigned NOT NULL,
  `child_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`listitem_id`, `child_id`),
  KEY `list_id` (`list_id`),
  KEY `child_id` (`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_lists` (
  `list_id` int(11) unsigned NOT NULL auto_increment,
  `owner_id` int(11) unsigned NOT NULL,
  `child_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`list_id`),
  KEY `owner_id` (`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */
