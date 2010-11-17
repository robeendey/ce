<?php

class Install_Import_Version3_EventPhotoTags extends Install_Import_Version3_AbstractTags
{
  protected $_fromResourceType = 'eventmedia';

  protected $_toResourceType = 'event_photo';
}

/*
CREATE TABLE IF NOT EXISTS `se_eventmediatags` (
  `eventmediatag_id` int(10) unsigned NOT NULL auto_increment,
  `eventmediatag_eventmedia_id` int(10) unsigned NOT NULL default '0',
  `eventmediatag_user_id` int(10) unsigned NOT NULL default '0',
  `eventmediatag_x` int(10) unsigned NOT NULL default '0',
  `eventmediatag_y` int(10) unsigned NOT NULL default '0',
  `eventmediatag_height` int(10) unsigned NOT NULL default '0',
  `eventmediatag_width` int(10) unsigned NOT NULL default '0',
  `eventmediatag_text` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `eventmediatag_date` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`eventmediatag_id`),
  KEY `INDEX` (`eventmediatag_eventmedia_id`,`eventmediatag_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */