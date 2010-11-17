<?php

class Install_Import_Version3_ClassifiedComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'classified';

  protected $_toResourceType = 'classified';
}

/*
CREATE TABLE IF NOT EXISTS `se_classifiedcomments` (
  `classifiedcomment_id` int(10) unsigned NOT NULL auto_increment,
  `classifiedcomment_classified_id` int(10) unsigned NOT NULL default '0',
  `classifiedcomment_authoruser_id` int(10) unsigned NOT NULL default '0',
  `classifiedcomment_date` int(10) unsigned NOT NULL default '0',
  `classifiedcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`classifiedcomment_id`),
  KEY `INDEX` (`classifiedcomment_classified_id`,`classifiedcomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */