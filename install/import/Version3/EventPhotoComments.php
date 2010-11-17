<?php

class Install_Import_Version3_EventPhotoComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'eventmedia';

  protected $_toResourceType = 'event_photo';
}

/*
CREATE TABLE IF NOT EXISTS `se_eventmediacomments` (
  `eventmediacomment_id` int(10) unsigned NOT NULL auto_increment,
  `eventmediacomment_eventmedia_id` int(10) unsigned NOT NULL default '0',
  `eventmediacomment_authoruser_id` int(10) unsigned NOT NULL default '0',
  `eventmediacomment_date` int(10) unsigned NOT NULL default '0',
  `eventmediacomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`eventmediacomment_id`),
  KEY `INDEX` (`eventmediacomment_eventmedia_id`,`eventmediacomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */