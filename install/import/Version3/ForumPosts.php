<?php

class Install_Import_Version3_ForumPosts extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_forumposts';

  protected $_toTable = 'engine4_forum_posts';

  protected $_fromJoins = array(
    'se_forummedia' => 'forumpost_forummedia_id=forummedia_id',
    'se_forumtopics' => 'forumpost_forumtopic_id=forumtopic_id'
  );

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['post_id'] = $data['forumpost_id'];
    $newData['topic_id'] = $data['forumpost_forumtopic_id'];
    $newData['user_id'] = $data['forumpost_authoruser_id'];
    $newData['forum_id'] = $data['forumtopic_forum_id'];
    $newData['body'] = htmlspecialchars_decode($data['forumpost_body']);
    $newData['creation_date'] = $this->_translateTime($data['forumpost_date']);
    $newData['modified_date'] = $this->_translateTime($data['forumpost_date']);

    // Attachment
    if( !empty($data['forummedia_ext']) ) {
      $file = $this->_getFromUserDir($data['forummedia_id'], 'uploads_forum', $data['forummedia_ext']);
      if( file_exists($file) ) {
        try {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'forum_post',
            'parent_id' => $data['forumpost_id'],
            'user_id' => $data['forumpost_authoruser_id'],
          ));
        } catch( Exception $e ) {
          $this->_error($e);
          $file_id = null;
        }
        
        if( $file_id ) {
          $newData['file_id'] = $file_id;
        }
      }
    }

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('forum_post', @$newData['post_id'], null, @$newData['body']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_forumposts` (
*  `forumpost_id` int(9) NOT NULL auto_increment,
*  `forumpost_forumtopic_id` int(9) NOT NULL default '0',
*  `forumpost_authoruser_id` int(9) NOT NULL default '0',
*  `forumpost_date` int(14) NOT NULL default '0',
  `forumpost_excerpt` varchar(100) collate utf8_unicode_ci NOT NULL default '',
*  `forumpost_body` text collate utf8_unicode_ci,
  `forumpost_forummedia_id` int(9) NOT NULL default '0',
  `forumpost_deleted` int(1) NOT NULL default '0',
  PRIMARY KEY  (`forumpost_id`),
  KEY `INDEX` (`forumpost_forumtopic_id`,`forumpost_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_forum_posts` (
*  `post_id` int(11) unsigned NOT NULL auto_increment,
*  `topic_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `body` text NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `file_id` int(11) unsigned NOT NULL default '0',
  `edit_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 *
 */









/*
CREATE TABLE IF NOT EXISTS `se_forummedia` (
  `forummedia_id` int(10) unsigned NOT NULL auto_increment,
  `forummedia_forumtopic_id` int(10) unsigned NOT NULL default '0',
  `forummedia_ext` varchar(8) collate utf8_unicode_ci NOT NULL default '',
  `forummedia_filesize` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`forummedia_id`),
  KEY `INDEX` (`forummedia_forumtopic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */