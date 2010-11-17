<?php

class Install_Import_Version3_GroupPosts extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groupposts';

  protected $_toTable = 'engine4_group_posts';

  protected $_fromJoins = array(
    'se_grouptopics' => 'grouptopic_id=grouppost_grouptopic_id',
  );

  protected function  _translateRow(array $data, $key = null)
  {
    // Do not import if no topic
    if( empty($data['grouptopic_group_id']) ) {
      return false;
    }

    $newData = array();
    
    $newData['post_id'] = $data['grouppost_id'];
    $newData['topic_id'] = $data['grouppost_grouptopic_id'];
    $newData['group_id'] = $data['grouptopic_group_id'];
    $newData['user_id'] = $data['grouppost_authoruser_id'];
    $newData['body'] = htmlspecialchars_decode($data['grouppost_body']);
    $newData['creation_date'] = $this->_translateTime($data['grouppost_date']);
    $newData['modified_date'] = $this->_translateTime($data['grouppost_date']);

    // search
    //if( @$newData['search'] ) {
    //  $this->_insertSearch('group_post', @$newData['post_id'], null, @$newData['body']);
    //}
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_groupposts` (
*  `grouppost_id` int(9) NOT NULL auto_increment,
*  `grouppost_grouptopic_id` int(9) NOT NULL default '0',
*  `grouppost_authoruser_id` int(9) NOT NULL default '0',
*  `grouppost_date` int(14) NOT NULL default '0',
  `grouppost_lastedit_date` int(14) NOT NULL default '0',
  `grouppost_lastedit_user_id` int(9) NOT NULL default '0',
*  `grouppost_body` text collate utf8_unicode_ci,
  `grouppost_deleted` int(1) NOT NULL default '0',
  PRIMARY KEY  (`grouppost_id`),
  KEY `INDEX` (`grouppost_grouptopic_id`,`grouppost_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_posts` (
*  `post_id` int(11) unsigned NOT NULL auto_increment,
*  `topic_id` int(11) unsigned NOT NULL,
*  `group_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,

*  `body` text NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
  PRIMARY KEY  (`post_id`),
  KEY `topic_id` (`topic_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */