<?php

class Install_Import_Version3_CoreReferrers extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_statrefs';

  protected $_toTable = 'engine4_core_referrers';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    // Parse
    $urlInfo = parse_url($data['statref_url']);
    if( empty($urlInfo['host']) ) {
      return false;
    }

    $newData['host'] = (string) @$urlInfo['host'];
    $newData['path'] = (string) @$urlInfo['path'];
    $newData['query'] = (string) @$urlInfo['query'];
    $newData['value'] = $data['statref_hits'];

    $this->_insertOrUpdate($this->getToDb(), $this->getToTable(), $newData, array(
      'value' => new Zend_Db_Expr('value+' . $newData['value']),
    ));
    
    return true;
    
    //return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_statrefs` (
-  `statref_id` int(9) NOT NULL auto_increment,
*  `statref_hits` int(9) NOT NULL default '0',
*  `statref_url` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`statref_id`),
  UNIQUE KEY `statref_url` (`statref_url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_core_referrers` (
*  `host` varchar(64) NOT NULL,
*  `path` varchar(64) NOT NULL,
*  `query` varchar(128) NOT NULL,
*  `value` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`host`,`path`,`query`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */