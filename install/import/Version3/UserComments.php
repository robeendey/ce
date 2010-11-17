<?php

class Install_Import_Version3_UserComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'profile';

  protected $_toResourceType = 'user';
}

/*
CREATE TABLE IF NOT EXISTS `se_profilecomments` (
  `profilecomment_id` int(9) NOT NULL auto_increment,
  `profilecomment_user_id` int(9) NOT NULL default '0',
  `profilecomment_authoruser_id` int(9) NOT NULL default '0',
  `profilecomment_date` int(14) NOT NULL default '0',
  `profilecomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`profilecomment_id`),
  KEY `profilecomment_user_id` (`profilecomment_user_id`,`profilecomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */