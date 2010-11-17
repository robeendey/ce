<?php

class Install_Import_Version3_CoreStatistics extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_stats';

  protected $_toTable = 'engine4_core_statistics';

  protected function  _translateRow(array $data, $key = null)
  {
    // Insert views
    $this->getToDb()->insert($this->getToTable(), array(
      'type' => 'core.views',
      'date' => $this->_translateTime($data['stat_date']),
      'value' => $data['stat_views'],
    ));
    
    // Insert login
    $this->getToDb()->insert($this->getToTable(), array(
      'type' => 'user.logins',
      'date' => $this->_translateTime($data['stat_date']),
      'value' => $data['stat_logins'],
    ));

    // Insert signups
    $this->getToDb()->insert($this->getToTable(), array(
      'type' => 'user.creations',
      'date' => $this->_translateTime($data['stat_date']),
      'value' => $data['stat_signups'],
    ));

    // Insert friends
    $this->getToDb()->insert($this->getToTable(), array(
      'type' => 'user.friendships',
      'date' => $this->_translateTime($data['stat_date']),
      'value' => $data['stat_friends'],
    ));
    
    // Cancel standard
    return false;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_stats` (
  `stat_id` int(9) NOT NULL auto_increment,
*  `stat_date` int(9) NOT NULL default '0',
*  `stat_views` int(9) NOT NULL default '0',
*  `stat_logins` int(9) NOT NULL default '0',
*  `stat_signups` int(9) NOT NULL default '0',
*  `stat_friends` int(9) NOT NULL default '0',
  `stat_chat_requests` bigint(20) unsigned NOT NULL default '0',
  `stat_chat_cpu_time` float NOT NULL default '0',
  `stat_chat_bandwidth` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`stat_id`),
  UNIQUE KEY `stat_date` (`stat_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_core_statistics` (
  `type` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `date` datetime NOT NULL,
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`type`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */