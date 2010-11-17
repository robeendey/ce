<?php

class Install_Import_Version3_Networks extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_subnets';

  protected $_toTable = 'engine4_network_networks';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['network_id'] = $data['subnet_id'];
    $newData['title'] = $data['subnet_name'];
    $newData['assignment'] = 1;
    $newData['hide'] = 0;
    
    // @todo pattern
    // @todo member_count

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_subnets` (
*  `subnet_id` int(9) NOT NULL auto_increment,
*  `subnet_name` int(10) unsigned NOT NULL default '0',
  `subnet_field1_qual` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `subnet_field1_value` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `subnet_field2_qual` varchar(2) collate utf8_unicode_ci NOT NULL default '',
  `subnet_field2_value` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`subnet_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE `engine4_network_networks` (
*  `network_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `title` varchar(255) NOT NULL,
*  `description` varchar(255) NOT NULL,
  `field_id` int(11) unsigned NOT NULL default '0',
  `pattern` text NULL,
  `member_count` int(11) unsigned NOT NULL default '0',
*  `hide` tinyint(1) NOT NULL default '0',
*  `assignment` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`network_id`),
  KEY `assignment` (`assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */