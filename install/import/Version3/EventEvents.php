<?php

class Install_Import_Version3_EventEvents extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_events';

  protected $_toTable = 'engine4_event_events';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['event_id'] = $data['event_id'];
    $newData['title'] = $data['event_title'];
    $newData['description'] = $data['event_desc'];
    $newData['user_id'] = $data['event_user_id'];
    $newData['parent_type'] = 'user';
    $newData['parent_id'] = $data['event_user_id'];
    $newData['search'] = $data['event_search'];
    $newData['creation_date'] = $this->_translateTime($data['event_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['event_dateupdated']);
    $newData['starttime'] = $this->_translateTime($data['event_date_start']);
    $newData['endtime'] = $this->_translateTime($data['event_date_end']);
    $newData['host'] = $data['event_host'];
    $newData['location'] = $data['event_location'];
    $newData['view_count'] = $data['event_views'];
    $newData['member_count'] = $data['event_totalmembers'];

    // privacy
    $this->_insertPrivacy('event', $data['event_id'], 'view', $this->_translateEventPrivacy($data['event_privacy']));
    $this->_insertPrivacy('event', $data['event_id'], 'comment', $this->_translateEventPrivacy($data['event_comments']));
    
    // get photo
    if( !empty($data['event_photo']) ) {
      $file = $this->_getFromUserDir(
        $data['event_id'],
        'uploads_event',
        $data['event_photo']
      );
      if( file_exists($file) ) {
        try {
          if( $this->getParam('resizePhotos', true) ) {
            $file_id = $this->_translatePhoto($file, array(
              'parent_type' => 'event',
              'parent_id' => $data['event_id'],
              'user_id' => @$data['event_user_id'],
            ));
          } else {
            $file_id = $this->_translateFile($file, array(
              'parent_type' => 'event',
              'parent_id' => $data['event_id'],
              'user_id' => @$data['event_user_id'],
            ), true);
          }
        } catch( Exception $e ) {
          $file_id = null;
          $this->_warning($e->getMessage(), 1);
        }

        if( $file_id ) {
          $newData['photo_id'] = $file_id;
        }
      }
    }

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('event', @$newData['event_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_events` (
*  `event_id` int(10) unsigned NOT NULL auto_increment,
*  `event_user_id` int(10) unsigned NOT NULL default '0',
  `event_eventcat_id` int(10) unsigned NOT NULL default '0',
*  `event_datecreated` int(10) unsigned NOT NULL default '0',
*  `event_dateupdated` int(10) unsigned NOT NULL default '0',
*  `event_views` int(10) unsigned NOT NULL default '0',
*  `event_title` varchar(128) collate utf8_unicode_ci default NULL,
*  `event_desc` text collate utf8_unicode_ci,
*  `event_date_start` bigint(20) unsigned NOT NULL default '0',
*  `event_date_end` bigint(20) unsigned NOT NULL default '0',
*  `event_host` varchar(255) collate utf8_unicode_ci default NULL,
*  `event_location` text collate utf8_unicode_ci,
  `event_photo` varchar(16) collate utf8_unicode_ci default NULL,
*  `event_search` tinyint(3) unsigned NOT NULL default '0',
  `event_privacy` tinyint(3) unsigned NOT NULL default '0',
  `event_comments` tinyint(3) unsigned NOT NULL default '0',
  `event_inviteonly` tinyint(3) unsigned NOT NULL default '0',
  `event_upload` tinyint(3) unsigned NOT NULL default '0',
  `event_tag` tinyint(3) unsigned NOT NULL default '0',
  `event_invite` tinyint(3) unsigned NOT NULL default '0',
  `event_totalcomments` smallint(5) unsigned NOT NULL default '0',
*  `event_totalmembers` smallint(5) unsigned NOT NULL default '0',
  `event_title_cleaned` varchar(128) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`event_id`),
  KEY `INDEX` (`event_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_event_events` (
*  `event_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `title` varchar(128) NOT NULL,
*  `description` varchar(512) NOT NULL,
*  `user_id` int(11) unsigned NOT NULL,
*  `parent_type` varchar(64) NOT NULL,
*  `parent_id` int(11) unsigned NOT NULL,
*  `search` tinyint(1) NOT NULL default '1',
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `starttime` datetime NOT NULL,
*  `endtime` datetime NOT NULL,
*  `host` varchar(115) NOT NULL,
*  `location` varchar(115) NOT NULL,
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `member_count` int(11) unsigned NOT NULL default '0',
  `approval` tinyint(1) NOT NULL default '0',
  `invite` tinyint(1) NOT NULL default '0',
  `photo_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`event_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_type` (`parent_type`, `parent_id`),
  KEY `starttime` (`starttime`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */















/*
CREATE TABLE IF NOT EXISTS `se_eventstyles` (
  `eventstyle_id` int(10) unsigned NOT NULL auto_increment,
  `eventstyle_event_id` int(10) unsigned NOT NULL default '0',
  `eventstyle_css` text collate utf8_unicode_ci,
  PRIMARY KEY  (`eventstyle_id`),
  KEY `INDEX` (`eventstyle_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */























/*
CREATE TABLE IF NOT EXISTS `se_eventfields` (
  `eventfield_id` int(10) unsigned NOT NULL auto_increment,
  `eventfield_eventcat_id` int(10) unsigned NOT NULL default '0',
  `eventfield_order` smallint(5) unsigned NOT NULL default '0',
  `eventfield_dependency` int(10) unsigned NOT NULL default '0',
  `eventfield_title` int(10) unsigned NOT NULL default '0',
  `eventfield_desc` int(10) unsigned NOT NULL default '0',
  `eventfield_error` int(10) unsigned NOT NULL default '0',
  `eventfield_type` tinyint(3) unsigned NOT NULL default '0',
  `eventfield_style` varchar(255) collate utf8_unicode_ci default NULL,
  `eventfield_maxlength` smallint(5) unsigned NOT NULL default '0',
  `eventfield_link` varchar(255) collate utf8_unicode_ci default NULL,
  `eventfield_options` longtext collate utf8_unicode_ci,
  `eventfield_required` tinyint(3) unsigned NOT NULL default '0',
  `eventfield_regex` varchar(255) collate utf8_unicode_ci default NULL,
  `eventfield_html` varchar(255) collate utf8_unicode_ci default NULL,
  `eventfield_search` tinyint(3) unsigned NOT NULL default '0',
  `eventfield_signup` tinyint(3) unsigned NOT NULL default '0',
  `eventfield_display` tinyint(3) unsigned NOT NULL default '0',
  `eventfield_special` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventfield_id`),
  KEY `INDEX` (`eventfield_eventcat_id`,`eventfield_dependency`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */


/*
CREATE TABLE IF NOT EXISTS `se_eventvalues` (
  `eventvalue_id` int(10) unsigned NOT NULL auto_increment,
  `eventvalue_event_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`eventvalue_id`),
  KEY `INDEX` (`eventvalue_event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */