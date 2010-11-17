<?php

class Install_Import_Version3_EventComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'event';

  protected $_toResourceType = 'event';
}

/*
CREATE TABLE IF NOT EXISTS `se_eventcomments` (
  `eventcomment_id` int(10) unsigned NOT NULL auto_increment,
  `eventcomment_event_id` int(10) unsigned NOT NULL default '0',
  `eventcomment_authoruser_id` int(10) unsigned NOT NULL default '0',
  `eventcomment_date` int(10) unsigned NOT NULL default '0',
  `eventcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`eventcomment_id`),
  KEY `INDEX` (`eventcomment_event_id`,`eventcomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */