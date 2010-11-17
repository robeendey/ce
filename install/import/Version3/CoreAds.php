<?php

class Install_Import_Version3_CoreAds extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_ads';

  protected $_toTable = 'engine4_core_ads';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['ad_id'] = $data['ad_id'];
    $newData['name'] = $data['ad_name'];
    $newData['ad_campaign'] = $data['ad_id'];
    $newData['views'] = $data['ad_total_views'];
    $newData['clicks'] = $data['ad_total_clicks'];
    $newData['html_code'] = htmlspecialchars_decode($data['ad_html']);
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_ads` (
*  `ad_id` int(9) NOT NULL auto_increment,
*  `ad_name` varchar(250) collate utf8_unicode_ci NOT NULL default '',
*  `ad_date_start` varchar(15) collate utf8_unicode_ci NOT NULL default '',
*  `ad_date_end` varchar(15) collate utf8_unicode_ci NOT NULL default '',
*  `ad_paused` int(1) NOT NULL default '0',
*  `ad_limit_views` int(10) NOT NULL default '0',
*  `ad_limit_clicks` int(10) NOT NULL default '0',
*  `ad_limit_ctr` varchar(8) collate utf8_unicode_ci NOT NULL default '0',
*  `ad_public` int(1) NOT NULL default '0',
-  `ad_position` varchar(15) collate utf8_unicode_ci NOT NULL default '',
*  `ad_levels` text collate utf8_unicode_ci NOT NULL,
*  `ad_subnets` text collate utf8_unicode_ci NOT NULL,
*  `ad_html` text collate utf8_unicode_ci NOT NULL,
*  `ad_total_views` int(10) NOT NULL default '0',
*  `ad_total_clicks` int(10) NOT NULL default '0',
*  `ad_filename` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_core_ads` (
*  `ad_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `name` varchar(16) NOT NULL,
*  `ad_campaign` int(11) unsigned NOT NULL,
*  `views` int(11) unsigned NOT NULL default '0',
*  `clicks` int(11) unsigned NOT NULL default '0',
?  `media_type` varchar(255) NOT NULL,
*  `html_code` text NOT NULL,
*  `photo_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`ad_id`),
  KEY ad_campaign (`ad_campaign`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */
