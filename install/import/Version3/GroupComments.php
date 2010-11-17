<?php

class Install_Import_Version3_GroupComments extends Install_Import_Version3_AbstractComments
{
  protected $_fromResourceType = 'group';

  protected $_toResourceType = 'group';
}

/*
CREATE TABLE IF NOT EXISTS `se_groupcomments` (
  `groupcomment_id` int(9) NOT NULL auto_increment,
  `groupcomment_group_id` int(9) NOT NULL default '0',
  `groupcomment_authoruser_id` int(9) NOT NULL default '0',
  `groupcomment_date` int(14) NOT NULL default '0',
  `groupcomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`groupcomment_id`),
  KEY `INDEX` (`groupcomment_group_id`,`groupcomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */