<?php

class Install_Import_Version3_BlogCategories extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_blogentrycats';

  protected $_toTable = 'engine4_blog_categories';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['category_id'] = $data['blogentrycat_id'];
    $newData['user_id'] = $data['blogentrycat_user_id'];

    if( !empty($data['blogentrycat_languagevar_id']) ) {
      $newData['category_name'] = (string) $this->_getLanguageValue($data['blogentrycat_languagevar_id']);
    } else {
      $newData['category_name'] = (string) @$data['blogentrycat_title'];
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_blogentrycats` (
*  `blogentrycat_id` int(10) unsigned NOT NULL auto_increment,
*  `blogentrycat_user_id` int(10) unsigned NOT NULL default '0',
*  `blogentrycat_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
*  `blogentrycat_languagevar_id` int(10) unsigned NOT NULL default '0',
  `blogentrycat_parentcat_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`blogentrycat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_blog_categories` (
*  `category_id` int(11) NOT NULL auto_increment,
*  `user_id` int(11) unsigned NOT NULL,
*  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */