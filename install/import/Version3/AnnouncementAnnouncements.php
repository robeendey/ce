<?php

class Install_Import_Version3_AnnouncementAnnouncements extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_announcements';

  protected $_toTable = 'engine4_announcement_announcements';

  protected $_priority = 40;

  protected $_adminIdentity;

  public function __sleep()
  {
    return array_merge(parent::__sleep(), array(
      '_adminIdentity',
    ));
  }
  
  protected function _run()
  {
    $this->_adminIdentity = $this->getToDb()->select()
      ->from('engine4_users', 'user_id')
      ->where('level_id = ?', 1) // Get from levels table?
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    parent::_run();
  }

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['announcement_id'] = $data['announcement_id'];
    $newData['creation_date'] = (string) $this->_translateTime(strtotime($data['announcement_date']));
    $newData['title'] = $data['announcement_subject'];
    $newData['body'] = htmlspecialchars_decode($data['announcement_body']);
    $newData['user_id'] = $this->_adminIdentity;

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_announcements` (
*  `announcement_id` int(9) NOT NULL auto_increment,
-  `announcement_order` int(9) NOT NULL default '0',
*  `announcement_date` varchar(255) collate utf8_unicode_ci NOT NULL default '0',
*  `announcement_subject` varchar(255) collate utf8_unicode_ci NOT NULL default '',
*  `announcement_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`announcement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_announcement_announcements` (
*  `announcement_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
*  `title` varchar(255) NOT NULL,
*  `body` text NOT NULL,
*  `creation_date` datetime NOT NULL,
-  `modified_date` datetime NULL,
  PRIMARY KEY  (`announcement_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */