<?php

class Install_Import_Version3_ClassifiedFields extends Install_Import_Version3_AbstractFields
{
  protected $_toTableTruncate = false;

  protected $_fromResourceType = 'classified';

  protected $_fromAlternateResourceType = 'classified';

  protected $_toResourceType = 'classified';

  protected $_useProfileType = true;
}


/*
CREATE TABLE IF NOT EXISTS `se_classifiedcats` (
  `classifiedcat_id` int(10) unsigned NOT NULL auto_increment,
  `classifiedcat_dependency` int(10) unsigned NOT NULL default '0',
  `classifiedcat_title` int(10) unsigned NOT NULL default '0',
  `classifiedcat_order` smallint(5) unsigned NOT NULL default '0',
  `classifiedcat_signup` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_classifiedfields` (
  `classifiedfield_id` int(10) unsigned NOT NULL auto_increment,
  `classifiedfield_classifiedcat_id` int(10) unsigned NOT NULL default '0',
  `classifiedfield_order` smallint(5) unsigned NOT NULL default '0',
  `classifiedfield_dependency` int(10) unsigned NOT NULL default '0',
  `classifiedfield_title` int(10) unsigned NOT NULL default '0',
  `classifiedfield_desc` int(10) unsigned NOT NULL default '0',
  `classifiedfield_error` int(10) unsigned NOT NULL default '0',
  `classifiedfield_type` tinyint(3) unsigned NOT NULL default '0',
  `classifiedfield_style` varchar(255) collate utf8_unicode_ci default NULL,
  `classifiedfield_maxlength` smallint(5) unsigned NOT NULL default '0',
  `classifiedfield_link` varchar(255) collate utf8_unicode_ci default NULL,
  `classifiedfield_options` longtext collate utf8_unicode_ci,
  `classifiedfield_required` tinyint(3) unsigned NOT NULL default '0',
  `classifiedfield_regex` varchar(255) collate utf8_unicode_ci default NULL,
  `classifiedfield_html` varchar(255) collate utf8_unicode_ci default NULL,
  `classifiedfield_search` tinyint(3) unsigned NOT NULL default '0',
  `classifiedfield_signup` tinyint(3) unsigned NOT NULL default '0',
  `classifiedfield_display` tinyint(3) unsigned NOT NULL default '0',
  `classifiedfield_special` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedfield_id`),
  KEY `INDEX` (`classifiedfield_classifiedcat_id`,`classifiedfield_dependency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_fields_maps` (
  `field_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `order` smallint(6) NOT NULL,
  PRIMARY KEY  (`field_id`,`option_id`,`child_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_fields_meta` (
  `field_id` int(11) NOT NULL auto_increment,

  `type` varchar(24) collate latin1_general_ci NOT NULL,
  `label` varchar(64) NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `alias` varchar(32) NOT NULL default '',
  `required` tinyint(1) NOT NULL default '0',
  `display` tinyint(1) unsigned NOT NULL,
  `search` tinyint(1) unsigned NOT NULL default '0',
  `order` smallint(3) unsigned NOT NULL default '999',

  `config` text NOT NULL,
  `validators` text NULL,
  `filters` text NULL,

  `style` text NULL,
  `error` text NULL,

  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

/*
CREATE TABLE `engine4_classified_fields_options` (
  `option_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `order` smallint(6) NOT NULL default '999',
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_classified_fields_search` (
  `item_id` int(11) NOT NULL,
  `price` double NULL,
  `location` varchar(255) NULL,
  PRIMARY KEY  (`item_id`),
  KEY `price` (`price`),
  KEY `location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_classifiedvalues` (
  `classifiedvalue_id` int(10) unsigned NOT NULL auto_increment,
  `classifiedvalue_classified_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedvalue_id`),
  KEY `INDEX` (`classifiedvalue_classified_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_fields_values` (
  `item_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `index` smallint(3) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`item_id`,`field_id`,`index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */