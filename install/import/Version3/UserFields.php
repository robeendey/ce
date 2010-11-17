<?php

class Install_Import_Version3_UserFields extends Install_Import_Version3_AbstractFields
{
  protected $_toTableTruncate = false;

  protected $_fromResourceType = 'profile';

  protected $_fromAlternateResourceType = 'user';

  protected $_toResourceType = 'user';

  protected $_useProfileType = true;
}


/*
CREATE TABLE IF NOT EXISTS `se_profilecats` (
  `profilecat_id` int(9) NOT NULL auto_increment,
  `profilecat_title` int(9) NOT NULL default '0',
  `profilecat_dependency` int(9) NOT NULL default '0',
  `profilecat_order` int(2) NOT NULL default '0',
  `profilecat_signup` int(1) NOT NULL,
  PRIMARY KEY  (`profilecat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
 *
 */


/*
CREATE TABLE IF NOT EXISTS `se_profilefields` (
  `profilefield_id` int(9) NOT NULL auto_increment,
  `profilefield_profilecat_id` int(9) NOT NULL default '0',
  `profilefield_order` int(3) NOT NULL default '0',
  `profilefield_dependency` int(9) NOT NULL default '0',
  `profilefield_title` int(9) NOT NULL default '0',
  `profilefield_desc` int(9) NOT NULL default '0',
  `profilefield_error` int(9) NOT NULL default '0',
  `profilefield_type` int(1) NOT NULL default '0',
  `profilefield_signup` int(1) NOT NULL default '0',
  `profilefield_style` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_maxlength` int(3) NOT NULL default '0',
  `profilefield_link` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_options` longtext collate utf8_unicode_ci,
  `profilefield_display` int(1) NOT NULL default '1',
  `profilefield_required` int(1) NOT NULL default '0',
  `profilefield_regex` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_special` int(1) NOT NULL default '0',
  `profilefield_html` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilefield_search` int(1) NOT NULL default '1',
  PRIMARY KEY  (`profilefield_id`),
  KEY `INDEX` (`profilefield_profilecat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_profilevalues` (
  `profilevalue_id` int(9) NOT NULL auto_increment,
  `profilevalue_user_id` int(9) NOT NULL default '0',
  `profilevalue_1` varchar(250) collate utf8_unicode_ci default '',
  `profilevalue_2` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilevalue_3` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `profilevalue_4` date NOT NULL default '0000-00-00',
  `profilevalue_5` int(2) default '-1',
  `profilevalue_6` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`profilevalue_id`),
  KEY `INDEX` (`profilevalue_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 *
 */
