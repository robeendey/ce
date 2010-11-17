<?php

class Install_Import_Version3_ClassifiedCategories extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_classifiedcats';

  protected $_toTable = 'engine4_classified_categories';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['category_id'] = $data['classifiedcat_id'];
    $newData['user_id'] = 0;
    $newData['category_name'] = $this->_getLanguageValue($data['classifiedcat_title']);

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_classifiedcats` (
*  `classifiedcat_id` int(10) unsigned NOT NULL auto_increment,
  `classifiedcat_dependency` int(10) unsigned NOT NULL default '0',
*  `classifiedcat_title` int(10) unsigned NOT NULL default '0',
  `classifiedcat_order` smallint(5) unsigned NOT NULL default '0',
  `classifiedcat_signup` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classifiedcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 *
 */

/*
CREATE TABLE `engine4_classified_categories` (
*  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
*  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */
