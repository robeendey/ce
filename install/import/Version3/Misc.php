<?php

class Install_Import_Version3_Misc extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;

  protected function _run()
  {
    $this->_message('Not implemented', 2);
  }
  
  protected function _translateRow(array $data, $key = null)
  {
    return false;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_faqcats` (
  `faqcat_id` int(9) NOT NULL auto_increment,
  `faqcat_order` int(5) NOT NULL default '0',
  `faqcat_title` int(9) NOT NULL default '0',
  PRIMARY KEY  (`faqcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_faqs` (
  `faq_id` int(9) NOT NULL auto_increment,
  `faq_faqcat_id` int(9) NOT NULL default '0',
  `faq_order` int(5) NOT NULL default '0',
  `faq_subject` int(9) NOT NULL default '0',
  `faq_content` int(9) NOT NULL default '0',
  `faq_datecreated` int(14) NOT NULL default '0',
  `faq_dateupdated` int(14) NOT NULL default '0',
  `faq_views` int(9) NOT NULL default '0',
  PRIMARY KEY  (`faq_id`),
  KEY `faq_faqcat_id` (`faq_faqcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_languages` (
  `language_id` int(9) NOT NULL auto_increment,
  `language_code` varchar(8) collate utf8_unicode_ci NOT NULL default '',
  `language_name` varchar(20) collate utf8_unicode_ci NOT NULL default '0',
  `language_autodetect_regex` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `language_setlocale` varchar(10) collate utf8_unicode_ci NOT NULL,
  `language_default` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_languagevars` (
  `languagevar_id` int(9) unsigned NOT NULL default '0',
  `languagevar_language_id` int(9) NOT NULL default '0',
  `languagevar_value` text collate utf8_unicode_ci,
  `languagevar_default` text collate utf8_unicode_ci,
  UNIQUE KEY `INDEX` (`languagevar_id`,`languagevar_language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_logins` (
  `login_id` int(9) NOT NULL auto_increment,
  `login_email` varchar(70) collate utf8_unicode_ci NOT NULL default '',
  `login_date` int(14) NOT NULL default '0',
  `login_ip` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `login_result` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`login_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_notifys` (
  `notify_id` int(9) NOT NULL auto_increment,
  `notify_user_id` int(9) NOT NULL default '0',
  `notify_notifytype_id` int(9) NOT NULL default '0',
  `notify_object_id` int(9) NOT NULL,
  `notify_urlvars` varchar(250) collate utf8_unicode_ci NOT NULL default '0',
  `notify_text` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`notify_id`),
  KEY `notify_user_id` (`notify_user_id`),
  KEY `notify_object_id` (`notify_object_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_notifytypes` (
  `notifytype_id` int(9) NOT NULL auto_increment,
  `notifytype_icon` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `notifytype_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `notifytype_title` int(9) NOT NULL default '0',
  `notifytype_url` varchar(100) collate utf8_unicode_ci NOT NULL,
  `notifytype_desc` int(9) NOT NULL default '0',
  `notifytype_group` int(1) NOT NULL default '0',
  PRIMARY KEY  (`notifytype_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_plugins` (
  `plugin_id` int(9) NOT NULL auto_increment,
  `plugin_name` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `plugin_version` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `plugin_type` varchar(30) collate utf8_unicode_ci NOT NULL default '',
  `plugin_desc` text collate utf8_unicode_ci NOT NULL,
  `plugin_icon` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `plugin_menu_title` int(9) NOT NULL,
  `plugin_pages_main` text collate utf8_unicode_ci NOT NULL,
  `plugin_pages_level` text collate utf8_unicode_ci NOT NULL,
  `plugin_url_htaccess` text collate utf8_unicode_ci NOT NULL,
  `plugin_disabled` tinyint(1) NOT NULL default '0',
  `plugin_order` smallint(3) NOT NULL default '0',
  PRIMARY KEY  (`plugin_id`),
  UNIQUE KEY `plugin_type` (`plugin_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_session_auth` (
  `session_auth_key` char(40) collate utf8_unicode_ci NOT NULL,
  `session_auth_user_id` int(9) NOT NULL,
  `session_auth_ip` int(9) NOT NULL,
  `session_auth_ua` char(32) collate utf8_unicode_ci NOT NULL,
  `session_auth_type` tinyint(1) NOT NULL,
  `session_auth_time` int(9) NOT NULL,
  PRIMARY KEY  (`session_auth_key`),
  KEY `CLEANUP` (`session_auth_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_session_data` (
  `session_data_id` char(32) NOT NULL,
  `session_data_body` longtext NOT NULL,
  `session_data_expires` int(11) NOT NULL,
  PRIMARY KEY  (`session_data_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_stylesamples` (
  `stylesample_id` int(9) NOT NULL auto_increment,
  `stylesample_type` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `stylesample_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `stylesample_thumb` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `stylesample_css` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`stylesample_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_systememails` (
  `systememail_id` int(9) NOT NULL auto_increment,
  `systememail_name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `systememail_title` int(9) NOT NULL,
  `systememail_desc` int(9) NOT NULL,
  `systememail_subject` int(9) NOT NULL,
  `systememail_body` int(9) NOT NULL,
  `systememail_vars` varchar(250) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`systememail_id`),
  UNIQUE KEY `systememail_name` (`systememail_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_urls` (
  `url_id` int(9) NOT NULL auto_increment,
  `url_title` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `url_file` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `url_regular` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  `url_subdirectory` varchar(200) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`url_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_usersettings` (
  `usersetting_id` int(9) NOT NULL auto_increment,
  `usersetting_user_id` int(9) NOT NULL default '0',
  `usersetting_lostpassword_code` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `usersetting_lostpassword_time` int(14) NOT NULL default '0',
  `usersetting_notify_friendrequest` int(1) NOT NULL default '0',
  `usersetting_notify_message` int(1) NOT NULL default '0',
  `usersetting_notify_profilecomment` int(1) NOT NULL default '0',
  `usersetting_actions_dontpublish` text collate utf8_unicode_ci NOT NULL,
  `usersetting_actions_display` text collate utf8_unicode_ci NOT NULL,
  `usersetting_displayname_method` tinyint(1) NOT NULL default '1',
  `usersetting_notify_pollcomment` tinyint(4) NOT NULL default '1',
  `usersetting_notify_classifiedcomment` int(1) NOT NULL default '1',
  `usersetting_notify_forumreply` int(1) NOT NULL default '1',
  `usersetting_notify_blogcomment` int(1) NOT NULL default '1',
  `usersetting_notify_blogtrackback` tinyint(4) NOT NULL default '1',
  `usersetting_notify_newblogsubscriptionentry` tinyint(4) NOT NULL default '1',
  `usersetting_notify_eventinvite` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_eventcomment` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_eventmediacomment` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_eventmemberrequest` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_neweventtag` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_eventmediatag` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_notify_groupinvite` int(1) NOT NULL default '1',
  `usersetting_notify_groupcomment` int(1) NOT NULL default '1',
  `usersetting_notify_groupmediacomment` int(1) NOT NULL default '1',
  `usersetting_notify_groupmemberrequest` int(1) NOT NULL default '1',
  `usersetting_notify_newgrouptag` int(1) NOT NULL default '1',
  `usersetting_notify_groupmediatag` int(1) NOT NULL default '1',
  `usersetting_notify_grouppost` int(1) NOT NULL default '1',
  `usersetting_notify_mediacomment` int(1) NOT NULL default '1',
  `usersetting_notify_newtag` int(1) NOT NULL default '1',
  `usersetting_notify_mediatag` int(1) NOT NULL default '1',
  `usersetting_music_profile_autoplay` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_music_site_autoplay` tinyint(3) unsigned NOT NULL default '1',
  `usersetting_xspfskin_id` int(10) unsigned NOT NULL default '1',
  `usersetting_notify_videocomment` int(1) NOT NULL default '1',
  PRIMARY KEY  (`usersetting_id`),
  UNIQUE KEY `usersetting_user_id` (`usersetting_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_visitors` (
  `visitor_ip` int(11) NOT NULL default '0',
  `visitor_browser` char(32) character set ascii collate ascii_bin NOT NULL default '',
  `visitor_user_id` int(10) unsigned NOT NULL default '0',
  `visitor_user_username` varchar(64) collate utf8_unicode_ci default NULL,
  `visitor_user_displayname` varchar(128) collate utf8_unicode_ci default NULL,
  `visitor_lastactive` int(14) NOT NULL default '0',
  `visitor_invisible` tinyint(14) NOT NULL default '0',
  UNIQUE KEY `UNIQUE` (`visitor_ip`,`visitor_browser`,`visitor_user_id`),
  KEY `LASTACTIVE` (`visitor_lastactive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_xspfskins` (
  `xspfskin_id` int(10) unsigned NOT NULL auto_increment,
  `xspfskin_title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `xspfskin_desc` text collate utf8_unicode_ci,
  `xspfskin_height` smallint(5) unsigned NOT NULL default '0',
  `xspfskin_width` smallint(5) unsigned NOT NULL default '0',
  `xspfskin_version` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`xspfskin_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;
 * 
 */