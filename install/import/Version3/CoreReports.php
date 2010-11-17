<?php

class Install_Import_Version3_CoreReports extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_reports';

  protected $_toTable = 'engine4_core_reports';
  
  protected function _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['report_id'] = $data['report_id'];
    $newData['user_id'] = $data['report_user_id'];

    switch( $data['report_reason'] ) {
      case 1:
        $newData['category'] = 'spam';
        break;
      case 2:
        $newData['category'] = 'inappropriate';
        break;
      case 3:
        $newData['category'] = 'abuse';
        break;
      default:
      case 0:
        $newData['category'] = 'other';
        break;
    }

    $newData['subject_type'] = '';
    $newData['subject_id'] = 0;

    // @todo implement?
    //$newData['url'] = $data['report_url'];

    $newData['description'] = $data['report_details']
      . '<br />SEv3 URL: ' . PHP_EOL . $data['report_url'];

    $newData['creation_date'] = '0000-00-00 00:00';
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_reports` (
*  `report_id` int(9) NOT NULL auto_increment,
*  `report_user_id` int(9) NOT NULL default '0',
  `report_url` varchar(250) collate utf8_unicode_ci NOT NULL default '',
*  `report_reason` int(1) NOT NULL default '0',
  `report_details` text collate utf8_unicode_ci,
  PRIMARY KEY  (`report_id`),
  KEY `INDEX` (`report_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_core_reports` (
*  `report_id` int(11) NOT NULL auto_increment,
*  `user_id` int(11) NOT NULL,
*  `category` varchar(16) collate utf8_unicode_ci NOT NULL,
*  `description` text collate utf8_unicode_ci NOT NULL,
  `subject_type` varchar(32) character set latin1 collate latin1_bin NOT NULL,
  `subject_id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`report_id`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */
