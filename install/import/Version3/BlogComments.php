<?php

class Install_Import_Version3_BlogComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'blog';

  protected $_toResourceType = 'blog';
}

/*
CREATE TABLE IF NOT EXISTS `se_blogcomments` (
  `blogcomment_id` int(10) unsigned NOT NULL auto_increment,
  `blogcomment_blogentry_id` int(10) unsigned NOT NULL default '0',
  `blogcomment_authoruser_id` int(10) unsigned NOT NULL default '0',
  `blogcomment_date` bigint(20) NOT NULL default '0',
  `blogcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`blogcomment_id`),
  KEY `INDEX` (`blogcomment_blogentry_id`,`blogcomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */