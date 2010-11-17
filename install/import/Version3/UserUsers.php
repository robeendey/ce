<?php

class Install_Import_Version3_UserUsers extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_users';

  protected $_toTable = 'engine4_users';

  protected $_priority = 6000;
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['user_id'] = $data['user_id'];
    $newData['email'] = $data['user_email'];
    $newData['username'] = trim($data['user_username']);
    $newData['displayname'] = trim((string) @$data['user_displayname']);
    if( empty($newData['displayname']) && !empty($newData['username']) ) {
      $newData['displayname'] = $newData['username'];
    }

    $newData['status'] = trim((string) @$data['user_status']);
    $newData['status_date'] = $this->_translateTime($data['user_status_date']);

    $newData['search'] = $data['user_search'] || !$data['user_invisible'];

    $newData['enabled'] = $data['user_enabled'];
    $newData['verified'] = $data['user_verified'];

    $newData['creation_date'] = $this->_translateTime($data['user_signupdate']);
    $newData['creation_ip'] = ip2long($data['user_ip_signup']);

    $newData['lastlogin_date'] = $this->_translateTime($data['user_lastlogindate']);
    $newData['lastlogin_ip'] = ip2long($data['user_ip_lastactive']);

    $newData['show_profileviewers'] = $data['user_saveviews'];

    // Get level
    $levelIdentity = $this->_getLevelMap($data['user_level_id']);
    if( !$levelIdentity ) {
      $levelIdentity = 4;
    }
    $newData['level_id'] = $levelIdentity;


    // Import file
    if( !empty($data['user_photo']) ) {
      $file = $this->_getFromUserDir(
        $data['user_id'],
        'uploads_user',
        $data['user_photo']
      );

      if( file_exists($file) ) {
        try {
          if( $this->getParam('resizePhotos', true) ) {
            $file_id = $this->_translatePhoto($file, array(
              'parent_type' => 'user',
              'parent_id' => $data['user_id'],
              'user_id' => $data['user_id'],
            ));
          } else {
            $file_id = $this->_translateFile($file, array(
              'parent_type' => 'user',
              'parent_id' => $data['user_id'],
              'user_id' => $data['user_id'],
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


    // Member count
    $newData['member_count'] = $this->getFromDb()->select()
      ->from('se_friends', new Zend_Db_Expr('COUNT(*)'))
      ->where('friend_user_id1 = ?', $data['user_id'])
      ->where('friend_status = ?', 1)
      ->query()
      ->fetchColumn(0)
      ;


    // View Count
    $newData['view_count'] = (int) $this->getFromDb()->select()
      ->from('se_profileviews', 'profileview_views')
      ->where('profileview_user_id = ?', $data['user_id'])
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    
    // privacy
    try {
      $this->_insertPrivacy('user', $data['user_id'], 'view', $this->_translatePrivacy($data['user_privacy']));
      $this->_insertPrivacy('user', $data['user_id'], 'comment', $this->_translatePrivacy($data['user_comments']));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['user_id'] . ' : ' . $e->getMessage());
    }
    
    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('user', @$newData['user_id'], @$newData['displayname'], @$newData['status']);
    }
    
    return $newData;
  }
}



/*
CREATE TABLE IF NOT EXISTS `se_users` (
*  `user_id` int(9) NOT NULL auto_increment,
*  `user_level_id` int(9) NOT NULL default '0',
*  `user_subnet_id` int(9) NOT NULL default '0',
  `user_profilecat_id` int(9) NOT NULL default '0',
*  `user_email` varchar(70) collate utf8_unicode_ci NOT NULL default '',
-  `user_newemail` varchar(70) collate utf8_unicode_ci NOT NULL default '',
-  `user_fname` varchar(64) collate utf8_unicode_ci NOT NULL default '',
-  `user_lname` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `user_username` varchar(64) collate utf8_unicode_ci NOT NULL default '',
*  `user_displayname` varchar(128) collate utf8_unicode_ci default NULL,
*  `user_password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
*  `user_password_method` tinyint(1) NOT NULL default '0',
*  `user_code` varchar(255) collate utf8_unicode_ci NOT NULL default '',
*  `user_enabled` int(1) NOT NULL default '0',
*  `user_verified` int(1) NOT NULL default '0',
  `user_language_id` int(9) NOT NULL default '0',
  `user_signupdate` int(14) NOT NULL default '0',
*  `user_lastlogindate` int(14) NOT NULL default '0',
  `user_lastactive` int(14) NOT NULL default '0',
*  `user_ip_signup` varchar(15) collate utf8_unicode_ci NOT NULL default '',
*  `user_ip_lastactive` varchar(15) collate utf8_unicode_ci NOT NULL default '',
*  `user_status` varchar(190) collate utf8_unicode_ci NOT NULL default '',
*  `user_status_date` int(14) NOT NULL default '0',
  `user_logins` int(9) NOT NULL default '0',
  `user_invitesleft` int(3) NOT NULL default '0',
  `user_timezone` varchar(5) collate utf8_unicode_ci NOT NULL default '',
  `user_dateupdated` int(14) NOT NULL default '0',
*  `user_blocklist` text collate utf8_unicode_ci,
*  `user_invisible` int(1) NOT NULL default '0',
*  `user_saveviews` int(1) NOT NULL default '0',
*  `user_photo` varchar(10) collate utf8_unicode_ci NOT NULL default '',
*  `user_search` int(1) NOT NULL default '0',
*  `user_privacy` int(2) NOT NULL default '0',
*  `user_comments` int(2) NOT NULL default '0',
-  `user_hasnotifys` tinyint(1) NOT NULL default '0',
-  `user_profile_album` enum('tab','side') collate utf8_unicode_ci NOT NULL default 'tab',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_username` (`user_username`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE `engine4_users` (
*  `user_id` int(11) unsigned NOT NULL auto_increment,
*  `email` varchar(128) NOT NULL,
*  `username` varchar(128) NOT NULL,
*  `displayname` varchar(128) NOT NULL default '',
*  `photo_id` int(11) unsigned NOT NULL default '0',
*  `status` text NULL,
*  `status_date` datetime NULL,
?  `password` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
?  `salt` char(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `locale` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default 'auto',
  `language` varchar(8) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default 'en_US',
  `timezone` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL default 'America/Los_Angeles',
*  `search` tinyint(1) NOT NULL default '1',
*  `show_profileviewers` tinyint(1) NOT NULL default '1',
*  `level_id` int(11) unsigned NOT NULL,
  `invites_used` int(11) unsigned NOT NULL default '0',
  `extra_invites` int(11) unsigned NOT NULL DEFAULT '0',
*  `enabled` tinyint(1) NOT NULL default '1',
*  `verified` tinyint(1) NOT NULL default '0',
*  `creation_date` datetime NOT NULL,
*  `creation_ip` bigint(11) NOT NULL,
  `modified_date` datetime NOT NULL,
*  `lastlogin_date` datetime default NULL,
*  `lastlogin_ip` int(11) default NULL,
  `update_date` int(11) default NULL,
*  `member_count` smallint(5) unsigned NOT NULL default '0',
*  `view_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `EMAIL` (`email`),
  UNIQUE KEY `USERNAME` (`username`),
  KEY `MEMBER_COUNT` (`member_count`),
  KEY `CREATION_DATE` (`creation_date`),
  KEY `search` (`search`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */















/*
CREATE TABLE IF NOT EXISTS `se_admins` (
  `admin_id` int(9) NOT NULL auto_increment,
  `admin_username` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_password` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_password_method` tinyint(1) NOT NULL default '0',
  `admin_code` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `admin_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `admin_email` varchar(70) collate utf8_unicode_ci NOT NULL default '',
  `admin_enabled` tinyint(1) NOT NULL default '1',
  `admin_language_id` smallint(3) NOT NULL default '1',
  `admin_lostpassword_code` varchar(15) collate utf8_unicode_ci NOT NULL default '',
  `admin_lostpassword_time` int(14) NOT NULL default '0',
  PRIMARY KEY  (`admin_id`),
  UNIQUE KEY `UNIQUE` (`admin_username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

INSERT INTO `se_admins` (`admin_id`, `admin_username`, `admin_password`, `admin_password_method`, `admin_code`, `admin_name`, `admin_email`, `admin_enabled`, `admin_language_id`, `admin_lostpassword_code`, `admin_lostpassword_time`) VALUES
(1, 'admin', 'this is an md5', 1, 'this is a salt', 'Administrator', 'email@domain.com', 1, 1, '', 0);
 *
 */

/*
CREATE TABLE IF NOT EXISTS `se_profilestyles` (
  `profilestyle_id` int(9) NOT NULL auto_increment,
  `profilestyle_user_id` int(9) NOT NULL default '0',
  `profilestyle_css` text collate utf8_unicode_ci,
  `profilestyle_stylesample_id` int(9) NOT NULL default '0',
  PRIMARY KEY  (`profilestyle_id`),
  KEY `profilestyle_user_id` (`profilestyle_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_profileviews` (
  `profileview_user_id` int(1) NOT NULL,
  `profileview_views` int(9) NOT NULL,
  `profileview_viewers` text collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `profileview_user_id` (`profileview_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */