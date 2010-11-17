<?php

class Install_Import_Version3_EventMembership extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_eventmembers';

  protected $_toTable = 'engine4_event_membership';

  protected function  _translateRow(array $data, $key = null)
  {
    // v3 codes:
    //  -1 -> Awaiting Approval
    //  0  -> Awaiting Response
    //  1  -> Attending
    //  2  -> Maybe Attending
    //  3  -> Not Attending
    //  4  -> I'll be late

    // v4 codes:
    //  0  -> Not attending
    //  1  -> Maybe Attending
    //  2  -> Attending
    //  3  -> Awaiting Response

    // Fix for previously imported events:
    // Note: change the 0 in "resource_id <= 0" to the last event created before migration
    //  UPDATE engine4_event_membership SET rsvp = 10 WHERE rsvp = 3 && resource_id <= 0;
    //  UPDATE engine4_event_membership SET rsvp = 11 WHERE rsvp = 2 && resource_id <= 0;
    //  UPDATE engine4_event_membership SET rsvp = 12 WHERE rsvp = 1 && resource_id <= 0;
    //  UPDATE engine4_event_membership SET rsvp = 13 WHERE rsvp <= 0 && resource_id <= 0;
    //  UPDATE engine4_event_membership SET rsvp = 11 WHERE rsvp < 10 && resource_id <= 0;
    //  UPDATE engine4_event_membership SET rsvp = rsvp - 10 WHERE rsvp >= 10 && resource_id <= 0;
    
    $newData = array();

    $newData['resource_id'] = $data['eventmember_event_id'];
    $newData['user_id'] = $data['eventmember_user_id'];
    $newData['active'] = $data['eventmember_status'] && $data['eventmember_approved'];
    $newData['resource_approved'] = $data['eventmember_approved'];
    $newData['user_approved'] = $data['eventmember_status'];
    $newData['title'] = $data['eventmember_title'];

    //$newData['rsvp'] = $data['eventmember_rsvp'];
    switch( @$data['eventmember_rsvp'] ) {
      case -1: case 0:
        $newData['rsvp'] = 3;
        break;
      case 1:
        $newData['rsvp'] = 2;
        break;
      case 2: case 4: default:
        $newData['rsvp'] = 1;
        break;
      case 3:
        $newData['rsvp'] = 0;
        break;
    }


    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_eventmembers` (
  `eventmember_id` int(10) unsigned NOT NULL auto_increment,
*  `eventmember_user_id` int(10) unsigned NOT NULL default '0',
*  `eventmember_event_id` int(10) unsigned NOT NULL default '0',
*  `eventmember_status` tinyint(3) unsigned NOT NULL default '0',
*  `eventmember_approved` tinyint(3) unsigned NOT NULL default '0',
  `eventmember_rank` tinyint(3) unsigned NOT NULL default '0',
*  `eventmember_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `eventmember_rsvp` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventmember_id`),
  KEY `INDEX` (`eventmember_user_id`,`eventmember_event_id`),
  KEY `STATUS` (`eventmember_status`,`eventmember_approved`,`eventmember_rsvp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_event_membership` (
*  `resource_id` int(11) unsigned NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `active` tinyint(1) NOT NULL default '0',
*  `resource_approved` tinyint(1) NOT NULL default '0',
*  `user_approved` tinyint(1) NOT NULL default '0',
  `message` text NULL,
*  `rsvp` tinyint(3) NOT NULL default '1',
*  `title` text NULL,
  PRIMARY KEY  (`resource_id`, `user_id`),
  KEY `REVERSE` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */
