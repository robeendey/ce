<?php

class Install_Import_Version3_VideoComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'video';

  protected $_toResourceType = 'video';
}

/*
CREATE TABLE IF NOT EXISTS `se_videocomments` (
  `videocomment_id` int(10) unsigned NOT NULL auto_increment,
  `videocomment_video_id` int(10) unsigned NOT NULL,
  `videocomment_authoruser_id` int(9) unsigned default NULL,
  `videocomment_date` int(14) NOT NULL default '0',
  `videocomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`videocomment_id`),
  KEY `INDEX` (`videocomment_video_id`,`videocomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */
