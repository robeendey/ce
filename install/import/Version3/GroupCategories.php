<?php

class Install_Import_Version3_GroupCategories extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_groupcats';

  protected $_toTable = 'engine4_group_categories';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['category_id'] = $data['groupcat_id'];
    $newData['title'] = $this->_getLanguageValue($data['groupcat_title']);

    return $newData;
  }
}


/*
CREATE TABLE IF NOT EXISTS `se_groupcats` (
*  `groupcat_id` int(9) NOT NULL auto_increment,
  `groupcat_dependency` int(9) NOT NULL default '0',
*  `groupcat_title` int(10) unsigned NOT NULL default '0',
  `groupcat_order` smallint(5) unsigned NOT NULL default '0',
  `groupcat_signup` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`groupcat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_group_categories` (
*  `category_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(64) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */

