<?php

class Install_Import_Version3_MessageMessages extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_pms';

  protected $_toTable = 'engine4_messages_messages';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['message_id'] = $data['pm_id'];
    $newData['conversation_id'] = ( isset($data['pm_pmconvo_id']) ? $data['pm_pmconvo_id'] : @$data['pm_convo_id'] );
    $newData['user_id'] = $data['pm_authoruser_id'];
    $newData['title'] = '';
    $newData['body'] = $data['pm_body'];
    $newData['date'] = $this->_translateTime($data['pm_date']);

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_pms` (
*  `pm_id` int(9) NOT NULL auto_increment,
*  `pm_authoruser_id` int(9) NOT NULL default '0',
*  `pm_pmconvo_id` int(9) NOT NULL default '0',
*  `pm_date` int(14) NOT NULL default '0',
*  `pm_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`pm_id`),
  KEY `pm_pmconvo_id` (`pm_pmconvo_id`),
  KEY `list_subquery` (`pm_pmconvo_id`,`pm_authoruser_id`,`pm_id`),
  FULLTEXT KEY `SEARCH` (`pm_body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE `engine4_messages_messages` (
*  `message_id` int(11) unsigned NOT NULL auto_increment,
*  `conversation_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
*  `body` text NOT NULL,
*  `date` datetime NOT NULL,
  `attachment_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci default '',
  `attachment_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`message_id`),
  UNIQUE KEY `CONVERSATIONS` (`conversation_id`,`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */