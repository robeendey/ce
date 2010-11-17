<?php

class Install_Import_Version3_EventCategories extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_eventcats';

  protected $_toTable = 'engine4_event_categories';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['category_id'] = $data['eventcat_id'];
    $newData['title'] = $this->_getLanguageValue($data['eventcat_title']);

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_eventcats` (
*  `eventcat_id` int(10) unsigned NOT NULL auto_increment,
  `eventcat_dependency` int(10) unsigned NOT NULL default '0',
*  `eventcat_title` int(10) unsigned NOT NULL default '0',
  `eventcat_order` smallint(5) unsigned NOT NULL default '0',
  `eventcat_signup` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_event_categories` (
*  `category_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */