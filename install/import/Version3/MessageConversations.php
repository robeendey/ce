<?php

class Install_Import_Version3_MessageConversations extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_pmconvos';

  protected $_toTable = 'engine4_messages_conversations';

  protected $_hasTitleColumn = false;

  protected $_hasUserIdentityColumn = false;

  protected function _initPost()
  {
    $meta = $this->getToDb()->describeTable($this->getToTable());
    if( isset($meta['title']) ) {
      $this->_hasTitleColumn = true;
    }
    if( isset($meta['user_id']) ) {
      $this->_hasUserIdentityColumn = true;
    }
  }

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['conversation_id'] = $data['pmconvo_id'];
    $newData['recipients'] = (int) $data['pmconvo_recipients'] - 1;

    if( $this->_hasTitleColumn ) {
      $newData['title'] = $data['pmconvo_subject'];
    }

    // Get first message date
    $firstMessageRow = $this->getFromDb()->select()
      ->from('se_pms', array('pm_id', 'pm_date'))
      ->where('pm_pmconvo_id = ?', $data['pmconvo_id'])
      ->order('pm_id ASC')
      ->limit(1)
      ->query()
      ->fetch()
      ;
    
    if( !empty($firstMessageRow) ) {
      $newData['modified'] = $this->_translateTime($firstMessageRow['pm_date']);
      if( $this->_hasUserIdentityColumn ) {
        $newData['user_id'] = $firstMessageRow['pm_authoruser_id'];
      }

      // Try to update title in first message
      $this->getToDb()->update('engine4_messages_messages', array(
        'title' => $data['pmconvo_subject'],
      ), array(
        'conversation_id = ?' => $data['pmconvo_id'],
        'message_id = ?' => $firstMessageRow['pm_id'],
      ));
      // Try to update title in not first message
      $this->getToDb()->update('engine4_messages_messages', array(
        'title' => 'Re: ' . $data['pmconvo_subject'],
      ), array(
        'conversation_id = ?' => $data['pmconvo_id'],
        'message_id != ?' => $firstMessageRow['pm_id'],
      ));
    } else {
      $newData['modified'] = '0000-00-00 00:00';
      if( $this->_hasUserIdentityColumn ) {
        $newData['user_id'] = 0;
      }
    }

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_pmconvos` (
*  `pmconvo_id` int(9) NOT NULL auto_increment,
*  `pmconvo_subject` varchar(100) collate utf8_unicode_ci NOT NULL default '',
*  `pmconvo_recipients` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`pmconvo_id`),
  KEY `pmconvo_recipients` (`pmconvo_recipients`),
  FULLTEXT KEY `SEARCH` (`pmconvo_subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_messages_conversations` (
*  `conversation_id` int(11) unsigned NOT NULL auto_increment,
*  `recipients` int(11) unsigned NOT NULL,
*  `modified` datetime NOT NULL,
  `locked` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`conversation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */