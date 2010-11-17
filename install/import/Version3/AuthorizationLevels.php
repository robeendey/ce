<?php

class Install_Import_Version3_AuthorizationLevels extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;

  protected $_priority = 7000;

  protected $_originalDefaultLevel;

  protected $_fromTable = 'se_levels';

  protected $_toTable = 'engine4_authorization_levels';

  public function __sleep()
  {
    return array_merge(parent::__sleep(), array(
      '_originalDefaultLevel',
    ));
  }

  protected function _initPre()
  {
    // Delete any non-standard levels
    $this->getToDb()->delete('engine4_authorization_levels', array(
      'level_id > ?' => 5,
    ));
    $this->getToDb()->delete('engine4_authorization_permissions', array(
      'level_id > ?' => 5,
    ));

    // Get the standard default level
    $this->_originalDefaultLevel = $this->getToDb()->select()
      ->from('engine4_authorization_levels', 'level_id')
      ->where('flag = ?', 'default')
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;
  }
  
  protected function _translateRow(array $data, $key = null)
  {
    $newData = array();
    
    $newData['title'] = $data['level_name'];
    $newData['description'] = $data['level_desc'];
    $newData['type'] = 'user';
    
    if( !empty($data['default']) ) {
      // Remove previous default
      $this->getToDb()->update('engine4_authorization_levels', array(
        'flag' => null,
      ), array(
        'flag' => 'default',
      ));
      $newData['flag'] = 'default';
    }

    // Insert
    $this->getToDb()->insert('engine4_authorization_levels', $newData);
    $levelIdentity = $this->getToDb()->lastInsertId();
    $this->_setLevelMap($data['level_id'], $levelIdentity);

    // Pull original default level info for defaults
    $defaultPermissions = $this->getToDb()->select()
      ->from('engine4_authorization_permissions')
      ->where('level_id = ?', $this->_originalDefaultLevel)
      ->query()
      ->fetchAll();
    $currentPermissions = array();
    foreach( $defaultPermissions as $defaultPermission ) {
      $defaultPermission['level_id'] = $levelIdentity;
      $currentPermissions[$defaultPermission['type']][$defaultPermission['name']] = $defaultPermission;
    }



    
    // Apply modifications

    // Messages
    $currentPermissions['messages']['create']['value'] = !empty($data['level_message_allow']);

    // User
    $currentPermissions['user']['style']['value'] = !empty($data['level_profile_style']);
    $currentPermissions['user']['block']['value'] = !empty($data['level_profile_block']);
    $currentPermissions['user']['delete']['value'] = !empty($data['level_profile_delete']);

    $currentPermissions['user']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_privacy']));
    $currentPermissions['user']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_comments']));

    

    // Album
    if( array_key_exists('level_album_allow', $data) ) {
      $currentPermissions['album']['create']['value'] = !empty($data['level_album_allow']);
      $currentPermissions['album']['edit']['value'] = !empty($data['level_album_allow']);
      $currentPermissions['album']['delete']['value'] = !empty($data['level_album_allow']);
      $currentPermissions['album']['view']['value'] = 1;

      $currentPermissions['album']['tag']['value'] = !empty($data['level_album_tag']);

      $currentPermissions['album']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_album_privacy'], 'owner'));
      $currentPermissions['album']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_album_comments'], 'owner'));
    }
    
    // Blog
    if( array_key_exists('level_blog_create', $data) ) {
      $currentPermissions['blog']['create']['value'] = !empty($data['level_blog_create']);
      $currentPermissions['blog']['edit']['value'] = !empty($data['level_blog_create']);
      $currentPermissions['blog']['delete']['value'] = !empty($data['level_blog_create']);
      $currentPermissions['blog']['view']['value'] = !empty($data['level_blog_view']);

      $currentPermissions['blog']['css']['value'] = $data['level_blog_style'];
      $currentPermissions['blog']['style']['value'] = $data['level_blog_style']; // Implemented? replace css with style

      $currentPermissions['blog']['auth_html']['params'] = $data['level_blog_html'];
      $currentPermissions['blog']['max']['params'] = $data['level_blog_entries'];
      $currentPermissions['blog']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_blog_privacy'], 'owner'));
      $currentPermissions['blog']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_blog_comments'], 'owner'));
    }
    
    // Chat
    if( array_key_exists('level_chat_allow', $data) ) {
      $currentPermissions['chat']['chat']['value'] = !empty($data['level_chat_allow']);
      $currentPermissions['chat']['im']['value'] = !empty($data['level_im_allow']);
    }

    // Classified
    if( array_key_exists('level_classified_allow', $data) ) {
      $currentPermissions['classified']['create']['value'] = (bool) ( (int) $data['level_classified_allow'] & 3 );
      $currentPermissions['classified']['edit']['value'] = (bool) ( (int) $data['level_classified_allow'] & 3 );
      $currentPermissions['classified']['delete']['value'] = (bool) ( (int) $data['level_classified_allow'] & 3 );
      $currentPermissions['classified']['view']['value'] = (bool) ( (int) $data['level_classified_allow'] & 1 );

      $currentPermissions['classified']['photo']['value'] = $data['level_classified_photo'];

      $currentPermissions['classified']['max']['params'] = $data['level_classified_entries'];
      $currentPermissions['classified']['auth_html']['params'] = $data['level_classified_html']; // Implemented?
      $currentPermissions['classified']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_classified_privacy'], 'owner'));
      $currentPermissions['classified']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_classified_comments'], 'owner'));
    }
    
    // Event
    if( array_key_exists('level_event_allow', $data) ) {
      $currentPermissions['event']['create']['value'] = (bool) ( (int) $data['level_event_allow'] & 7 );
      $currentPermissions['event']['edit']['value'] = (bool) ( (int) $data['level_event_allow'] & 7 );
      $currentPermissions['event']['delete']['value'] = (bool) ( (int) $data['level_event_allow'] & 7 );
      $currentPermissions['event']['view']['value'] = (bool) ( (int) $data['level_event_allow'] & 1 );

      $currentPermissions['event']['style']['value'] = !empty($data['level_event_style']);
      $currentPermissions['event']['photo']['value'] = !empty($data['level_event_upload']);

      $currentPermissions['event']['auth_view']['params'] = Zend_Json::encode($this->_translateEventPrivacyPermission($data['level_event_privacy']));
      $currentPermissions['event']['auth_comment']['params'] = Zend_Json::encode($this->_translateEventPrivacyPermission($data['level_event_comments']));
    }
    
    // Forum

    // Group
    if( array_key_exists('level_group_allow', $data) ) {
      $currentPermissions['group']['create']['value'] = (bool) ( (int) $data['level_group_allow'] & 7 );
      $currentPermissions['group']['edit']['value'] = (bool) ( (int) $data['level_group_allow'] & 7 );
      $currentPermissions['group']['delete']['value'] = (bool) ( (int) $data['level_group_allow'] & 7 );
      $currentPermissions['group']['view']['value'] = (bool) ( (int) $data['level_group_allow'] & 1 );

      $currentPermissions['group']['style']['value'] = !empty($data['level_group_style']);
      $currentPermissions['group']['photo']['value'] = !empty($data['level_group_upload']);

      $currentPermissions['group']['auth_view']['params'] = Zend_Json::encode($this->_translateGroupPrivacyPermission($data['level_group_privacy']));
      $currentPermissions['group']['auth_comment']['params'] = Zend_Json::encode($this->_translateGroupPrivacyPermission($data['level_group_comments']));
    }

    // Music
    if( array_key_exists('level_music_allow', $data) ) {
      $currentPermissions['music_playlist']['create']['value'] = !empty($data['level_music_allow']);
      $currentPermissions['music_playlist']['edit']['value'] = !empty($data['level_music_allow']);
      $currentPermissions['music_playlist']['delete']['value'] = !empty($data['level_music_allow']);
      $currentPermissions['music_playlist']['view']['value'] = 1;

      $currentPermissions['music_playlist']['max_songs']['params'] = $data['level_music_maxnum'];
      $currentPermissions['music_playlist']['max_filesize']['params'] = $data['level_music_maxsize'];
      $currentPermissions['music_playlist']['max_storage']['params'] = $data['level_music_storage'];
      // There are none for music before, take user
      $currentPermissions['music_playlist']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_privacy'], 'owner'));
      $currentPermissions['music_playlist']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_comments'], 'owner'));
    }
    
    // Poll
    if( array_key_exists('level_poll_allow', $data) ) {
      $currentPermissions['poll']['create']['value'] = (bool) ( (int) $data['level_poll_allow'] & 7 );
      $currentPermissions['poll']['edit']['value'] = (bool) ( (int) $data['level_poll_allow'] & 7 );
      $currentPermissions['poll']['delete']['value'] = (bool) ( (int) $data['level_poll_allow'] & 7 );
      $currentPermissions['poll']['view']['value'] = (bool) ( (int) $data['level_poll_allow'] & 1 );

      $currentPermissions['poll']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_poll_privacy'], 'owner'));
      $currentPermissions['poll']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_poll_comments'], 'owner'));
    }
    // Video
    if( array_key_exists('level_video_allow', $data) ) {
      $currentPermissions['video']['create']['value'] = !empty($data['level_video_allow']);
      $currentPermissions['video']['edit']['value'] = !empty($data['level_video_allow']);
      $currentPermissions['video']['delete']['value'] = !empty($data['level_video_allow']);
      $currentPermissions['video']['view']['value'] = 1;

      $currentPermissions['video']['max']['params'] = !empty($data['level_video_maxnum']);
      $currentPermissions['video']['auth_view']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_privacy']));
      $currentPermissions['video']['auth_comment']['params'] = Zend_Json::encode($this->_translatePrivacyPermission($data['level_profile_comments']));
    }



    
    // Insert permissions
    foreach( $currentPermissions as $type => $typeInfo ) {
      foreach( $typeInfo as $name => $permData ) {
        if( !empty($permData['type']) ) {
          $this->getToDb()->insert('engine4_authorization_permissions', $permData);
        } else {
          $this->_warning("Missing permission data for permission: $type ($name).  This plugin may not be installed in your SocialEngine 4 site yet.", 1);
        }
      }
    }
    
    return true;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_levels` (
  `level_id` int(9) NOT NULL auto_increment,
*  `level_name` varchar(50) collate utf8_unicode_ci NOT NULL default '',
*  `level_desc` text collate utf8_unicode_ci NOT NULL,
*  `level_default` int(1) NOT NULL default '0',
  `level_signup` int(1) NOT NULL default '0',
*  `level_message_allow` int(1) NOT NULL default '0',
  `level_message_inbox` int(3) NOT NULL default '0',
  `level_message_outbox` int(3) NOT NULL default '0',
  `level_message_recipients` int(3) NOT NULL default '1',
*  `level_profile_style` int(1) NOT NULL default '0',
  `level_profile_style_sample` int(1) NOT NULL default '0',
*  `level_profile_block` int(1) NOT NULL default '0',
  `level_profile_search` int(1) NOT NULL default '0',
*  `level_profile_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default '',
*  `level_profile_comments` varchar(100) collate utf8_unicode_ci NOT NULL default '',
  `level_profile_status` int(1) NOT NULL default '0',
  `level_profile_invisible` int(1) NOT NULL,
  `level_profile_views` int(1) NOT NULL,
  `level_profile_change` int(1) NOT NULL default '0',
*  `level_profile_delete` int(1) NOT NULL default '0',
  `level_photo_allow` int(1) NOT NULL default '0',
  `level_photo_width` varchar(3) collate utf8_unicode_ci NOT NULL default '',
  `level_photo_height` varchar(3) collate utf8_unicode_ci NOT NULL default '',
  `level_photo_exts` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `level_poll_allow` int(1) NOT NULL default '7',
  `level_poll_entries` int(3) NOT NULL default '10',
  `level_poll_search` int(1) NOT NULL default '1',
  `level_poll_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"7";i:3;s:2:"15";i:4;s:2:"31";i:5;s:2:"63";}',
  `level_poll_comments` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
*  `level_classified_allow` tinyint(1) NOT NULL default '3',
*  `level_classified_entries` smallint(3) NOT NULL default '50',
  `level_classified_search` tinyint(1) NOT NULL default '1',
*  `level_classified_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"7";i:3;s:2:"15";i:4;s:2:"31";i:5;s:2:"63";}',
*  `level_classified_comments` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
*  `level_classified_photo` tinyint(1) NOT NULL default '1',
  `level_classified_photo_width` varchar(3) collate utf8_unicode_ci NOT NULL default '500',
  `level_classified_photo_height` varchar(3) collate utf8_unicode_ci NOT NULL default '500',
  `level_classified_photo_exts` varchar(50) collate utf8_unicode_ci NOT NULL default '',
  `level_classified_album_exts` text collate utf8_unicode_ci,
  `level_classified_album_mimes` text collate utf8_unicode_ci,
  `level_classified_album_storage` bigint(14) NOT NULL default '5242880',
  `level_classified_album_maxsize` bigint(14) NOT NULL default '2048000',
  `level_classified_album_width` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_classified_album_height` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
*  `level_classified_html` text collate utf8_unicode_ci,
  `level_classified_style` tinyint(4) NOT NULL default '1',
*  `level_blog_view` tinyint(3) unsigned NOT NULL default '1',
*  `level_blog_create` tinyint(3) unsigned NOT NULL default '1',
*  `level_blog_entries` smallint(5) unsigned NOT NULL default '20',
*  `level_blog_style` tinyint(3) unsigned NOT NULL default '1',
  `level_blog_search` tinyint(3) unsigned NOT NULL default '1',
*  `level_blog_privacy` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"7";i:3;s:2:"15";i:4;s:2:"31";i:5;s:2:"63";}',
*  `level_blog_comments` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
  `level_blog_trackbacks_allow` tinyint(4) NOT NULL default '1',
  `level_blog_trackbacks_detect` tinyint(4) NOT NULL default '1',
*  `level_blog_html` text collate utf8_unicode_ci,
  `level_blog_category_create` tinyint(4) NOT NULL default '1',
*  `level_event_allow` tinyint(3) unsigned NOT NULL default '7',
  `level_event_photo` tinyint(3) unsigned NOT NULL default '1',
  `level_event_photo_width` varchar(3) collate utf8_unicode_ci NOT NULL default '200',
  `level_event_photo_height` varchar(3) collate utf8_unicode_ci NOT NULL default '200',
  `level_event_photo_exts` varchar(50) collate utf8_unicode_ci NOT NULL default 'jpeg,jpg,gif,png',
  `level_event_inviteonly` tinyint(3) unsigned NOT NULL default '1',
*  `level_event_style` tinyint(3) unsigned NOT NULL default '1',
  `level_event_album_exts` text collate utf8_unicode_ci,
  `level_event_album_mimes` text collate utf8_unicode_ci,
  `level_event_album_storage` bigint(20) NOT NULL default '5242880',
  `level_event_album_maxsize` bigint(20) NOT NULL default '2048000',
  `level_event_album_width` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_event_album_height` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_event_search` tinyint(3) unsigned NOT NULL default '1',
*  `level_event_privacy` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"3";i:1;s:1:"7";i:2;s:2:"15";i:3;s:2:"31";i:4;s:2:"63";i:5;s:3:"127";}',
*  `level_event_comments` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
  `level_event_html` text collate utf8_unicode_ci,
  `level_event_backdate` tinyint(1) unsigned NOT NULL default '0',
*  `level_event_upload` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
  `level_event_tag` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
*  `level_group_allow` tinyint(1) NOT NULL default '7',
  `level_group_photo` tinyint(1) NOT NULL default '1',
  `level_group_photo_width` varchar(3) collate utf8_unicode_ci NOT NULL default '200',
  `level_group_photo_height` varchar(3) collate utf8_unicode_ci NOT NULL default '200',
  `level_group_photo_exts` varchar(50) collate utf8_unicode_ci NOT NULL default 'jpeg,jpg,gif,png',
  `level_group_titles` int(1) NOT NULL default '1',
  `level_group_officers` int(1) NOT NULL default '1',
  `level_group_approval` int(1) NOT NULL default '1',
*  `level_group_style` int(1) NOT NULL default '1',
  `level_group_album_exts` text collate utf8_unicode_ci,
  `level_group_album_mimes` text collate utf8_unicode_ci,
  `level_group_album_storage` bigint(11) NOT NULL default '5242880',
  `level_group_album_maxsize` bigint(11) NOT NULL default '2048000',
  `level_group_album_width` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_group_album_height` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_group_maxnum` int(3) NOT NULL default '10',
  `level_group_search` int(1) NOT NULL default '1',
*  `level_group_privacy` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:3:"255";i:1;s:3:"127";i:2;s:2:"63";i:3;s:2:"31";i:4;s:2:"15";i:5;s:1:"7";}',
*  `level_group_comments` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
*  `level_group_upload` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
  `level_group_tag` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
  `level_group_discussion` varchar(128) collate utf8_unicode_ci NOT NULL default 'a:8:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";i:7;s:3:"127";}',
*  `level_album_allow` int(1) NOT NULL default '1',
  `level_album_maxnum` int(3) NOT NULL default '10',
  `level_album_exts` text collate utf8_unicode_ci NOT NULL,
  `level_album_mimes` text collate utf8_unicode_ci NOT NULL,
  `level_album_storage` bigint(11) NOT NULL default '5242880',
  `level_album_maxsize` bigint(11) NOT NULL default '2048000',
  `level_album_width` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_album_height` varchar(4) collate utf8_unicode_ci NOT NULL default '500',
  `level_album_style` int(1) NOT NULL default '1',
  `level_album_search` int(1) NOT NULL default '1',
*  `level_album_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"7";i:3;s:2:"15";i:4;s:2:"31";i:5;s:2:"63";}',
*  `level_album_comments` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
  `level_album_profile` set('side','tab') collate utf8_unicode_ci default NULL,
*  `level_album_tag` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
  `level_chat_allow` tinyint(3) unsigned NOT NULL default '1',
  `level_im_allow` tinyint(3) unsigned NOT NULL default '1',
*  `level_music_allow` tinyint(3) unsigned NOT NULL default '1',
*  `level_music_maxnum` smallint(5) unsigned NOT NULL default '5',
  `level_music_exts` text collate utf8_unicode_ci,
  `level_music_mimes` text collate utf8_unicode_ci,
*  `level_music_storage` bigint(20) unsigned NOT NULL default '104857600',
*  `level_music_maxsize` bigint(20) unsigned NOT NULL default '15728640',
  `level_music_allow_skins` tinyint(3) unsigned NOT NULL default '1',
  `level_xpfskin_default` int(10) unsigned NOT NULL default '3',
  `level_music_allow_downloads` tinyint(3) unsigned NOT NULL default '0',
*  `level_video_allow` tinyint(1) unsigned NOT NULL default '1',
*  `level_video_privacy` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:6:{i:0;s:1:"1";i:1;s:1:"3";i:2;s:1:"7";i:3;s:2:"15";i:4;s:2:"31";i:5;s:2:"63";}',
*  `level_video_comments` varchar(100) collate utf8_unicode_ci NOT NULL default 'a:7:{i:0;s:1:"0";i:1;s:1:"1";i:2;s:1:"3";i:3;s:1:"7";i:4;s:2:"15";i:5;s:2:"31";i:6;s:2:"63";}',
  `level_video_search` tinyint(1) unsigned NOT NULL default '1',
*  `level_video_maxnum` tinyint(5) unsigned NOT NULL default '100',
  `level_video_maxsize` int(10) unsigned NOT NULL default '20971520',
  `level_youtube_allow` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`level_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_authorization_levels` (
  `level_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('public','user','moderator','admin') NOT NULL default 'user',
  `flag` enum('default','superadmin','public') NULL,
  PRIMARY KEY  (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_authorization_allow` (
  `resource_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `resource_id` int(11) unsigned NOT NULL,
  `action` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `role` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `role_id` int(11) unsigned NOT NULL default '0',
  `value` tinyint(1) NOT NULL default '0',
  `params` text,
  PRIMARY KEY  (`resource_type`,`resource_id`,`action`,`role`, `role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */


/*
CREATE TABLE `engine4_authorization_permissions` (
  `level_id` int(11) unsigned NOT NULL,
  `type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` tinyint(3) NOT NULL default '0',
  `params` varchar(255) NULL,
  PRIMARY KEY  (`level_id`,`type`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 * 
 */