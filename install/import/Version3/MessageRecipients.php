<?php

class Install_Import_Version3_MessageRecipients extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_pmconvoops';

  protected $_toTable = 'engine4_messages_recipients';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['conversation_id'] = $data['pmconvoop_pmconvo_id'];
    $newData['user_id'] = $data['pmconvoop_user_id'];
    $newData['inbox_read'] = $data['pmconvoop_read'];
    $newData['inbox_deleted'] = $data['pmconvoop_deleted_inbox'];
    $newData['outbox_deleted'] = $data['pmconvoop_deleted_outbox'];

    // Get inbox message id
    $inboxRow = $this->getFromDb()->select()
      ->from('se_pms', array('pm_id', 'pm_date'))
      ->where('pm_pmconvo_id = ?', $data['pmconvoop_pmconvo_id'])
      ->where('pm_authoruser_id != ?', $data['pmconvoop_user_id'])
      ->order('pm_date DESC')
      ->limit(1)
      ->query()
      ->fetch()
      ;

    if( !empty($inboxRow) ) {
      $newData['inbox_message_id'] = $inboxRow['pm_id'];
      $newData['inbox_updated'] = $this->_translateTime($inboxRow['pm_date']);
    }

    // Get outbox message id
    $outboxRow = $this->getFromDb()->select()
      ->from('se_pms')
      ->where('pm_pmconvo_id = ?', $data['pmconvoop_pmconvo_id'])
      ->where('pm_authoruser_id = ?', $data['pmconvoop_user_id'])
      ->order('pm_date DESC')
      ->limit(1)
      ->query()
      ->fetch()
      ;

    if( !empty($outboxRow) ) {
      $newData['outbox_message_id'] = $outboxRow['pm_id'];
      $newData['outbox_updated'] = $this->_translateTime($outboxRow['pm_date']);
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_pmconvoops` (
  `pmconvoop_id` int(10) unsigned NOT NULL auto_increment,
*  `pmconvoop_pmconvo_id` int(10) unsigned NOT NULL default '0',
*  `pmconvoop_user_id` int(10) unsigned NOT NULL default '0',
*  `pmconvoop_read` tinyint(1) unsigned NOT NULL default '0',
*  `pmconvoop_deleted_inbox` tinyint(3) unsigned NOT NULL default '0',
*  `pmconvoop_deleted_outbox` tinyint(3) unsigned NOT NULL default '0',
-  `pmconvoop_pmdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pmconvoop_id`),
  UNIQUE KEY `INDEX` (`pmconvoop_pmconvo_id`,`pmconvoop_user_id`),
  KEY `total_outbox` (`pmconvoop_user_id`,`pmconvoop_deleted_outbox`,`pmconvoop_read`),
  KEY `last_pm_date` (`pmconvoop_pmdate`),
  KEY `total_inbox` (`pmconvoop_user_id`,`pmconvoop_deleted_inbox`,`pmconvoop_read`,`pmconvoop_pmdate`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_messages_recipients` (
*  `user_id` int(11) unsigned NOT NULL,
*  `conversation_id` int(11) unsigned NOT NULL,
*  `inbox_message_id` int(11) unsigned default NULL,
*  `inbox_updated` datetime default NULL,
*  `inbox_read` tinyint(1) default NULL,
*  `inbox_deleted` tinyint(1) default NULL,
*  `outbox_message_id` int(11) unsigned default NULL,
*  `outbox_updated` datetime default NULL,
*  `outbox_deleted` tinyint(1) default NULL,
  PRIMARY KEY  (`user_id`,`conversation_id`),
  KEY `INBOX_UPDATED` (`user_id`,`conversation_id`,`inbox_updated`),
  KEY `OUTBOX_UPDATED` (`user_id`,`conversation_id`,`outbox_updated`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */