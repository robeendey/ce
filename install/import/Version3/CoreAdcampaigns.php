<?php

class Install_Import_Version3_CoreAdcampaigns extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_ads';

  protected $_toTable = 'engine4_core_adcampaigns';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();
    
    $newData['adcampaign_id'] = $data['ad_id'];
    $newData['name'] = $data['ad_name'];
    $newData['start_time'] = $this->_translateTime($data['ad_date_start']);
    $newData['end_time'] = $this->_translateTime($data['ad_date_end']);
    $newData['limit_view'] = $data['ad_limit_views'];
    $newData['limit_click'] = $data['ad_limit_clicks'];
    $newData['limit_ctr'] = $data['ad_limit_ctr'];
    $newData['views'] = $data['ad_total_views'];
    $newData['clicks'] = $data['ad_total_clicks'];
    $newData['public'] = $data['ad_public'];
    $newData['status'] = !$data['ad_paused'];
    $newData['end_settings'] = !empty($data['ad_date_end']);

    // campaign levels
    $levels = $this->_translateCommaStringToArray($data['ad_levels']);
    $levels = array_filter($levels, 'is_numeric');
    if( !empty($levels) && is_array($levels) ) {
      $newData['level'] = Zend_Json::encode($levels);
    }

    // campaign networks
    $networks = $this->_translateCommaStringToArray($data['ad_subnets']);
    $networks = array_filter($networks, 'is_numeric');
    if( !empty($networks) && is_array($networks) ) {
      $newData['network'] = Zend_Json::encode($networks);
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `engine4_core_adcampaigns` (
*  `adcampaign_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `end_settings` tinyint(4) NOT NULL,
*  `name` varchar(255) NOT NULL,
*  `start_time` datetime NOT NULL,
*  `end_time` datetime NOT NULL,
*  `limit_view` int(11) unsigned NOT NULL default '0',
*  `limit_click` int(11) unsigned NOT NULL default '0',
*  `limit_ctr` varchar(11) NOT NULL default '0',
*  `network` varchar(255) NOT NULL,
*  `level` varchar(255) NOT NULL,
*  `views` int(11) unsigned NOT NULL default '0',
*  `clicks` int(11) unsigned NOT NULL default '0',
*  `public` tinyint(4) NOT NULL default '0',
*  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`adcampaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */