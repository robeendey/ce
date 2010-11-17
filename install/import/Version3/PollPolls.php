<?php

class Install_Import_Version3_PollPolls extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_polls';

  protected $_toTable = 'engine4_poll_polls';

  protected function _initPre()
  {
    $this->_truncateTable($this->getToDb(), 'engine4_poll_votes');
    $this->_truncateTable($this->getToDb(), 'engine4_poll_options');
  }

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['poll_id'] = $data['poll_id'];
    $newData['user_id'] = $data['poll_user_id'];
    $newData['is_closed'] = $data['poll_closed'];
    $newData['title'] = $data['poll_title'];
    $newData['description'] = $data['poll_desc'];
    $newData['creation_date'] = $this->_translateTime($data['poll_datecreated']);
    $newData['views'] = $data['poll_views'];
    $newData['comment_count'] = $data['poll_totalcomments'];
    $newData['vote_count'] = $data['poll_totalvotes'];
    $newData['search'] = $data['poll_search'];

    
    // load poll data
    try {
      $pollOptions = $this->_unserialize($data['poll_options']);
      $pollAnswers = $this->_unserialize($data['poll_answers']);
      $pollVotes   = $this->_unserialize($data['poll_voted']);
    } catch( Exception $e ) {
      $pollOptions = array();
      $pollAnswers = array();
      $pollVotes   = array();
      $this->_warning($e->getMessage(), 1);
    }

    // Make sure there aren't any empty or duplicate voters
    $pollVotes   = array_unique(array_filter($pollVotes));

    // poll options
    $pollOptionIds = array();
    foreach( $pollOptions as $index => $pollOption ) {
      $this->getToDb()->insert('engine4_poll_options', array(
        'poll_id' => $data['poll_id'],
        'poll_option' => $pollOption,
        'votes' => (int) @$pollAnswers[$index],
      ));
      $pollOptionIds[$index] = $this->getToDb()->lastInsertId();
    }

    // poll votes
    // Note: we have to just make bogus votes since we didn't keep track of who
    // voted for what before
    $currentIndex = 0;
    $pollAnswersCopy = $pollAnswers;
    foreach( $pollVotes as $pollVote ) {
      foreach( $pollAnswersCopy as $index => $pollAnswer ) {
        if( $pollAnswer > 0 ) {
          $currentIndex = $index;
          $pollAnswersCopy[$index]--;
        }
      }
      $pollOptionId = $pollOptionIds[$currentIndex];

      try {
        $this->getToDb()->insert('engine4_poll_votes', array(
          'poll_id' => $data['poll_id'],
          'user_id' => $pollVote,
          'poll_option_id' => $pollOptionId,
          'creation_date' => $newData['creation_date'],
          'modified_date' => $newData['creation_date'],
        ));
      } catch( Exception $e ) {
        $this->_error('Failed adding poll vote for poll id ' . $data['poll_id'] . ' : ' . $e->getMessage());
      }
    }

    // privacy
    try {
      $this->_insertPrivacy('poll', $data['poll_id'], 'view', $this->_translatePrivacy($data['poll_privacy'], 'owner'));
      $this->_insertPrivacy('poll', $data['poll_id'], 'comment', $this->_translatePrivacy($data['poll_comments'], 'owner'));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['poll_id'] . ' : ' . $e->getMessage());
    }

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('poll', @$newData['poll_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}


/*
CREATE TABLE IF NOT EXISTS `se_polls` (
*  `poll_id` int(10) unsigned NOT NULL auto_increment,
*  `poll_user_id` int(10) unsigned NOT NULL default '0',
*  `poll_datecreated` bigint(20) unsigned NOT NULL default '0',
*  `poll_title` varchar(250) collate utf8_unicode_ci NOT NULL default '',
*  `poll_desc` text collate utf8_unicode_ci,
*  `poll_options` text collate utf8_unicode_ci,
*  `poll_answers` text collate utf8_unicode_ci,
*  `poll_voted` text collate utf8_unicode_ci,
*  `poll_search` tinyint(3) unsigned NOT NULL default '0',
*  `poll_privacy` smallint(5) unsigned NOT NULL default '0',
*  `poll_comments` smallint(5) unsigned NOT NULL default '0',
*  `poll_closed` tinyint(3) unsigned NOT NULL default '0',
*  `poll_totalvotes` int(10) unsigned NOT NULL default '0',
*  `poll_views` int(10) unsigned NOT NULL default '0',
*  `poll_totalcomments` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`poll_id`),
  KEY `INDEX` (`poll_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_poll_polls` (
*  `poll_id` int(11) unsigned NOT NULL auto_increment,
*  `user_id` int(11) unsigned NOT NULL,
*  `is_closed` tinyint(1) NOT NULL default '0',
*  `title` varchar(255) NOT NULL,
*  `description` text NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `views` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
*  `search` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`poll_id`),
  KEY `user_id` (`user_id`),
  KEY `is_closed` (`is_closed`),
  KEY `creation_date` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */

/*
DROP TABLE IF EXISTS `engine4_poll_options`;
CREATE TABLE IF NOT EXISTS `engine4_poll_options` (
  `poll_option_id` int(11) unsigned NOT NULL auto_increment,
  `poll_id` int(11) unsigned NOT NULL,
  `poll_option` text NOT NULL,
  `votes` smallint(4) unsigned NOT NULL,
  PRIMARY KEY  (`poll_option_id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_poll_votes` (
  `poll_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `poll_option_id` int(11) unsigned NOT NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  KEY `poll_option_id` (`poll_option_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */