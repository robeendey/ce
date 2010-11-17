<?php

class Install_Import_Version3_BlogBlogs extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_blogentries';

  protected $_toTable = 'engine4_blog_blogs';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['blog_id'] = $data['blogentry_id'];
    $newData['title'] = $data['blogentry_title'];
    $newData['body'] = htmlspecialchars_decode($data['blogentry_body']);
    $newData['owner_type'] = 'user';
    $newData['owner_id'] = $data['blogentry_user_id'];
    $newData['creation_date'] = $this->_translateTime($data['blogentry_date']);
    $newData['modified_date'] = $this->_translateTime($data['blogentry_date']);
    $newData['view_count'] = $data['blogentry_views'];
    $newData['comment_count'] = $data['blogentry_totalcomments'];
    $newData['search'] = $data['blogentry_search'];

    // privacy
    try {
      $this->_insertPrivacy('blog', $data['blogentry_id'], 'view', $this->_translatePrivacy($data['blogentry_privacy'], 'owner'));
      $this->_insertPrivacy('blog', $data['blogentry_id'], 'comment', $this->_translatePrivacy($data['blogentry_comments'], 'owner'));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['blogentry_id'] . ' : ' . $e->getMessage());
    }

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('blog', @$newData['blog_id'], @$newData['title'], @$newData['body']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_blogentries` (
*  `blogentry_id` int(10) unsigned NOT NULL auto_increment,
*  `blogentry_user_id` int(10) unsigned NOT NULL default '0',
  `blogentry_blogentrycat_id` int(10) unsigned NOT NULL default '0',
*  `blogentry_date` bigint(20) NOT NULL default '0',
*  `blogentry_views` int(10) unsigned NOT NULL default '0',
*  `blogentry_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
*  `blogentry_body` longtext collate utf8_unicode_ci,
*  `blogentry_search` tinyint(3) unsigned NOT NULL default '0',
*  `blogentry_privacy` tinyint(3) unsigned NOT NULL default '0',
*  `blogentry_comments` tinyint(3) unsigned NOT NULL default '0',
  `blogentry_trackbacks` text collate utf8_unicode_ci,
*  `blogentry_totalcomments` smallint(5) unsigned NOT NULL default '0',
  `blogentry_totaltrackbacks` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogentry_id`),
  KEY `LISTBYDATE` (`blogentry_user_id`,`blogentry_privacy`,`blogentry_date`),
  KEY `LISTBYCAT` (`blogentry_user_id`,`blogentry_blogentrycat_id`,`blogentry_privacy`,`blogentry_date`),
  KEY `blogentry_date` (`blogentry_date`),
  FULLTEXT KEY `SEARCH` (`blogentry_title`,`blogentry_body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
DROP TABLE IF EXISTS `engine4_blog_blogs`;
CREATE TABLE `engine4_blog_blogs` (
*  `blog_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(128) NOT NULL,
*  `body` longtext NOT NULL,
*  `owner_type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
*  `search` tinyint(1) NOT NULL default '1',
  `draft` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`blog_id`),
  KEY `owner_type` (`owner_type`, `owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

















/*
CREATE TABLE IF NOT EXISTS `se_blogpings` (
  `blogping_id` int(10) unsigned NOT NULL auto_increment,
  `blogping_blogentry_id` int(10) unsigned NOT NULL default '0',
  `blogping_target_url` text collate utf8_unicode_ci,
  `blogping_source_url` text collate utf8_unicode_ci,
  `blogping_status` tinyint(3) unsigned NOT NULL default '0',
  `blogping_type` tinyint(3) unsigned NOT NULL default '0',
  `blogping_ip` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`blogping_id`),
  KEY `INDEX` (`blogping_status`,`blogping_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_blogstyles` (
  `blogstyle_id` int(10) unsigned NOT NULL auto_increment,
  `blogstyle_user_id` int(10) unsigned NOT NULL default '0',
  `blogstyle_css` text collate utf8_unicode_ci,
  PRIMARY KEY  (`blogstyle_id`),
  KEY `INDEX` (`blogstyle_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_blogsubscriptions` (
  `blogsubscription_id` int(10) unsigned NOT NULL auto_increment,
  `blogsubscription_user_id` int(10) unsigned NOT NULL default '0',
  `blogsubscription_owner_id` int(10) unsigned NOT NULL default '0',
  `blogsubscription_date` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`blogsubscription_id`),
  UNIQUE KEY `INDEX` (`blogsubscription_user_id`,`blogsubscription_owner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_blogtrackbacks` (
  `blogtrackback_id` int(10) unsigned NOT NULL auto_increment,
  `blogtrackback_blogentry_id` int(10) unsigned NOT NULL default '0',
  `blogtrackback_name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `blogtrackback_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `blogtrackback_excerpt` text collate utf8_unicode_ci,
  `blogtrackback_excerpthash` varchar(32) collate utf8_unicode_ci NOT NULL default '',
  `blogtrackback_url` text collate utf8_unicode_ci,
  `blogtrackback_ip` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `blogtrackback_date` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`blogtrackback_id`),
  KEY `INDEX` (`blogtrackback_blogentry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */