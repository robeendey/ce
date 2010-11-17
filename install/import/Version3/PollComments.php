<?php

class Install_Import_Version3_PollComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'poll';

  protected $_toResourceType = 'poll';
}

/*
CREATE TABLE IF NOT EXISTS `se_pollcomments` (
  `pollcomment_id` int(9) unsigned NOT NULL auto_increment,
  `pollcomment_poll_id` int(9) unsigned NOT NULL default '0',
  `pollcomment_authoruser_id` int(9) unsigned NOT NULL default '0',
  `pollcomment_date` int(14) NOT NULL default '0',
  `pollcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`pollcomment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */