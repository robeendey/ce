<?php

class Install_Import_Version3_GroupPhotoComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'groupmedia';

  protected $_toResourceType = 'group_photo';
}


/*
CREATE TABLE IF NOT EXISTS `se_groupmediacomments` (
  `groupmediacomment_id` int(9) NOT NULL auto_increment,
  `groupmediacomment_groupmedia_id` int(9) NOT NULL default '0',
  `groupmediacomment_authoruser_id` int(9) NOT NULL default '0',
  `groupmediacomment_date` int(14) NOT NULL default '0',
  `groupmediacomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`groupmediacomment_id`),
  KEY `INDEX` (`groupmediacomment_groupmedia_id`,`groupmediacomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */