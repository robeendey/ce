<?php

class Install_Import_Version3_GroupPhotoTags extends Install_Import_Version3_AbstractTags
{
  protected $_fromResourceType = 'groupmedia';

  protected $_toResourceType = 'group_photo';
}


/*
CREATE TABLE IF NOT EXISTS `se_groupmediatags` (
  `groupmediatag_id` int(9) NOT NULL auto_increment,
  `groupmediatag_groupmedia_id` int(9) NOT NULL default '0',
  `groupmediatag_user_id` int(9) NOT NULL default '0',
  `groupmediatag_x` int(9) NOT NULL default '0',
  `groupmediatag_y` int(9) NOT NULL default '0',
  `groupmediatag_height` int(9) NOT NULL default '0',
  `groupmediatag_width` int(9) NOT NULL default '0',
  `groupmediatag_text` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `groupmediatag_date` int(14) NOT NULL default '0',
  PRIMARY KEY  (`groupmediatag_id`),
  KEY `INDEX` (`groupmediatag_groupmedia_id`,`groupmediatag_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */