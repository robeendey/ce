<?php

class Install_Import_Version3_CoreSettings extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;

  protected function _run()
  {
    $data = $this->getFromDb()->select()
      ->from('se_settings')
      ->order('setting_id ASC')
      ->limit(1)
      ->query()
      ->fetch()
      ;

    if( empty($data) ) {
      $this->_warning('No settings found', 0);
      return;
    }

    // Set settings

    // Activity
    $this->_setSetting('activity.userdelete', !empty($data['setting_actions_selfdelete']), 1);
    
    // Core
    $this->_setSetting('core.email.from', @$data['setting_email_fromemail'], 'no-reply@' . $_SERVER['HTTP_HOST']);
    $this->_setSetting('core.license.key', @$data['setting_key'], '');
    $this->_setSetting('core.general.browse', !empty($data['setting_permission_search']), 1);
    $this->_setSetting('core.general.commenthtml', @$data['setting_comment_html'], '');
    $this->_setSetting('core.general.portal', !empty($data['setting_permission_portal']), 1);
    $this->_setSetting('core.general.profile', !empty($data['setting_permission_profile']), 1);
    $this->_setSetting('core.general.search', !empty($data['setting_permission_search']), 1);
    $this->_setSetting('core.spam.censor', @$data['setting_banned_words'], '');
    $this->_setSetting('core.spam.comment', !empty($data['setting_comment_code']), 0);
    $this->_setSetting('core.spam.contact', !empty($data['setting_contact_code']), 0);
    $this->_setSetting('core.spam.invite', !empty($data['setting_invite_code']), 0);
    $this->_setSetting('core.spam.login', !empty($data['setting_login_code']), 0);
    $this->_setSetting('core.spam.signup', !empty($data['setting_signup_code']), 0);
    $this->_setSetting('core.spam.ipbans', @$data['setting_banned_ips'], '');

    // User
    $this->_setSetting('user.signup.approve', !empty($data['setting_signup_enable']), 1);
    $this->_setSetting('user.signup.checkemail', !empty($data['setting_signup_invite_checkemail']), 1);
    $this->_setSetting('user.signup.random', !empty($data['setting_signup_randpass']), 1);
    $this->_setSetting('user.signup.terms', !empty($data['setting_signup_tos']), 1);
    $this->_setSetting('user.signup.verifyemail', !empty($data['setting_signup_verify']), 1);


    // Chat
    if( array_key_exists('setting_chat_enabled', $data) ) {
      $this->_setSetting('chat.chat.enabled', !empty($data['setting_chat_enabled']));
      $this->_setSetting('chat.general.delay', @$data['setting_chat_update'], 5000);
      $this->_setSetting('chat.im.enabled', !empty($data['chat.im.enabled']));
    }
    
    // Video
    if( array_key_exists('setting_video_ffmpeg_path', $data) ) {
      $this->_setSetting('video.ffmpeg.path', @$data['setting_video_ffmpeg_path'], '');
      $this->_setSetting('video.jobs', @$data['setting_video_max_jobs'], 2);
    }


    
    // Get public level settings

    // Get public level id
    $publicLevelIdentity = $this->getToDb()->select()
      ->from('engine4_authorization_levels', 'level_id')
      ->where('flag = ?', 'public')
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    // User
    $this->_updateLevelPermission($publicLevelIdentity,
      'user', 'view', !empty($data['setting_permission_profile']));

    
    // Album
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'album', 'view', !empty($data['setting_permission_album']));
    }
    
    // Blog
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'blog', 'view', !empty($data['setting_permission_blog']));
    }

    // Classified
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'classified', 'view', !empty($data['setting_permission_classified']));
    }

    // Event
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'event', 'view', !empty($data['setting_permission_event']));
    }

    // Group
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'group', 'view', !empty($data['setting_permission_group']));
    }

    // Poll
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'poll', 'view', !empty($data['setting_permission_poll']));
    }

    // Video
    if( array_key_exists('setting_permission_album', $data) ) {
      $this->_updateLevelPermission($publicLevelIdentity,
        'video', 'view', !empty($data['setting_permission_video']));
    }

    
  }

  protected function _setSetting($key, $value, $default = null)
  {
    if( null === $value ) {
      $value = $default;
    }
    $this->_insertOrUpdate($this->getToDb(), 'engine4_core_settings', array(
      'name' => $key,
      'value' => $value,
    ), array(
      'value' => $value,
    ));
  }

  protected function _updateLevelPermission($levelIdentity, $type, $name, $value, $params = null)
  {
    if( !$levelIdentity ) return;
    $this->_insertOrUpdate($this->getToDb(), 'engine4_authorization_permissions', array(
      'level_id' => $levelIdentity,
      'type' => $type,
      'name' => $name,
      'value' => !empty($data['setting_permission_profile']),
      'params' => $params,
    ), array(
      'value' => !empty($data['setting_permission_profile']),
      'params' => $params,
    ));
  }
  
  protected function _translateRow(array $data, $key = null)
  {
    return false;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_settings` (
  `setting_id` int(9) NOT NULL auto_increment,
*  `setting_key` varchar(20) collate utf8_unicode_ci NOT NULL default '',
  `setting_version` varchar(16) collate utf8_unicode_ci NOT NULL default '',
  `setting_online` tinyint(1) NOT NULL default '1',
  `setting_url` tinyint(1) NOT NULL default '0',
  `setting_username` tinyint(1) NOT NULL default '1',
  `setting_password_method` tinyint(1) NOT NULL default '1',
  `setting_password_code_length` tinyint(2) NOT NULL default '16',
  `setting_lang_allow` int(1) NOT NULL default '1',
  `setting_lang_autodetect` tinyint(1) NOT NULL default '1',
  `setting_lang_anonymous` tinyint(1) NOT NULL default '1',
  `setting_timezone` varchar(5) collate utf8_unicode_ci NOT NULL default '-8',
  `setting_dateformat` varchar(20) collate utf8_unicode_ci NOT NULL default 'n/j/Y',
  `setting_timeformat` varchar(20) collate utf8_unicode_ci NOT NULL default 'g:i A',
*  `setting_permission_profile` tinyint(1) NOT NULL default '1',
  `setting_permission_invite` tinyint(1) NOT NULL default '1',
*  `setting_permission_search` tinyint(1) NOT NULL default '1',
*  `setting_permission_portal` tinyint(1) NOT NULL default '1',
*  `setting_banned_ips` text collate utf8_unicode_ci,
  `setting_banned_emails` text collate utf8_unicode_ci,
  `setting_banned_usernames` text collate utf8_unicode_ci,
*  `setting_banned_words` text collate utf8_unicode_ci,
*  `setting_comment_code` tinyint(1) NOT NULL default '0',
*  `setting_comment_html` varchar(250) collate utf8_unicode_ci NOT NULL default '',
  `setting_connection_allow` tinyint(1) NOT NULL default '3',
  `setting_connection_framework` tinyint(1) NOT NULL default '0',
  `setting_connection_types` text collate utf8_unicode_ci,
  `setting_connection_other` tinyint(1) NOT NULL default '1',
  `setting_connection_explain` tinyint(1) NOT NULL default '1',
  `setting_signup_photo` tinyint(1) NOT NULL default '0',
*  `setting_signup_enable` tinyint(1) NOT NULL default '1',
  `setting_signup_welcome` tinyint(1) NOT NULL default '1',
  `setting_signup_invite` tinyint(1) NOT NULL default '0',
*  `setting_signup_invite_checkemail` tinyint(1) NOT NULL default '0',
  `setting_signup_invite_numgiven` smallint(3) NOT NULL default '5',
  `setting_signup_invitepage` tinyint(1) NOT NULL default '0',
*  `setting_signup_verify` tinyint(1) NOT NULL default '0',
*  `setting_signup_code` tinyint(1) NOT NULL default '1',
*  `setting_signup_randpass` tinyint(1) NOT NULL default '0',
*  `setting_signup_tos` tinyint(1) NOT NULL default '1',
*  `setting_invite_code` tinyint(1) NOT NULL default '1',
  `setting_actions_showlength` int(14) NOT NULL default '2629743',
  `setting_actions_actionsperuser` smallint(2) NOT NULL default '7',
*  `setting_actions_selfdelete` smallint(2) NOT NULL default '1',
  `setting_actions_privacy` smallint(2) NOT NULL default '1',
  `setting_actions_actionsonprofile` smallint(2) NOT NULL default '7',
  `setting_actions_actionsinlist` smallint(2) NOT NULL default '35',
  `setting_actions_visibility` smallint(2) NOT NULL default '1',
  `setting_actions_preference` smallint(1) NOT NULL default '1',
  `setting_subnet_field1_id` int(9) NOT NULL default '-2',
  `setting_subnet_field2_id` int(9) NOT NULL default '-2',
  `setting_email_fromname` varchar(70) collate utf8_unicode_ci NOT NULL default '',
*  `setting_email_fromemail` varchar(70) collate utf8_unicode_ci NOT NULL default '',
  `setting_cache_enabled` tinyint(1) unsigned NOT NULL default '0',
  `setting_cache_default` varchar(32) collate utf8_unicode_ci default 'file',
  `setting_cache_lifetime` int(9) unsigned default '120',
  `setting_cache_file_options` text collate utf8_unicode_ci,
  `setting_cache_memcache_options` text collate utf8_unicode_ci,
  `setting_cache_xcache_options` text collate utf8_unicode_ci,
  `setting_session_options` text collate utf8_unicode_ci,
*  `setting_contact_code` tinyint(1) unsigned NOT NULL default '1',
*  `setting_login_code` tinyint(1) unsigned NOT NULL default '0',
  `setting_login_code_failedcount` smallint(2) unsigned NOT NULL default '0',
  `setting_stats_remote` tinyint(1) NOT NULL default '1',
  `setting_stats_remote_last` int(11) NOT NULL default '0',
*  `setting_permission_poll` tinyint(3) unsigned default '1',
  `setting_poll_html` text collate utf8_unicode_ci,
*  `setting_permission_classified` int(1) NOT NULL default '1',
  `setting_permission_forum` int(1) NOT NULL default '1',
  `setting_forum_code` int(1) NOT NULL default '1',
  `setting_forum_status` int(1) NOT NULL default '1',
  `setting_forum_modprivs` varchar(10) collate utf8_unicode_ci NOT NULL default '11111',
*  `setting_permission_blog` tinyint(4) NOT NULL default '1',
*  `setting_permission_event` tinyint(4) NOT NULL default '1',
*  `setting_permission_group` int(1) NOT NULL default '1',
  `setting_group_discussion_code` int(1) NOT NULL default '1',
  `setting_group_discussion_html` varchar(250) collate utf8_unicode_ci NOT NULL default '',
*  `setting_permission_album` int(1) NOT NULL default '1',
*  `setting_chat_enabled` tinyint(3) unsigned NOT NULL default '1',
*  `setting_chat_update` smallint(5) unsigned NOT NULL default '2000',
  `setting_chat_showphotos` tinyint(3) unsigned NOT NULL default '1',
*  `setting_im_enabled` tinyint(3) unsigned NOT NULL default '1',
  `setting_im_html` text collate utf8_unicode_ci,
*  `setting_permission_video` tinyint(1) unsigned NOT NULL default '1',
*  `setting_video_ffmpeg_path` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `setting_video_width` smallint(3) unsigned NOT NULL default '480',
  `setting_video_height` smallint(3) unsigned NOT NULL default '386',
  `setting_video_thumb_width` smallint(3) unsigned NOT NULL default '80',
  `setting_video_thumb_height` smallint(3) unsigned NOT NULL default '70',
  `setting_video_mimes` text collate utf8_unicode_ci,
  `setting_video_exts` text collate utf8_unicode_ci,
*  `setting_video_max_jobs` tinyint(2) unsigned NOT NULL default '3',
  `setting_video_cronjob` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;
 * 
 */

/*
CREATE TABLE `engine4_core_settings` (
  `name` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
INSERT IGNORE INTO `engine4_core_settings` (`name`, `value`) VALUES
('activity.notifications.template', 'Hello %title%,\n\n%body%\n\n--Site Admin'),
('authorization.defaultlevel', '4'),
('core.admin.reauthenticate', '0'),
('core.admin.mode', 'none'),
('core.admin.password', ''),
('core.admin.timeout', '600'),
('core.comet.enabled', '1'),
('core.comet.mode', 'short'),
('core.comet.delay', '1000'),
('core.comet.reconnect', '2000'),
('core.doctype', 'XHTML1_STRICT'),
('core.email.from', 'email@domain.com'),
('core.facebook.key', ''),
('core.facebook.secret', ''),
('core.general.commenthtml', ''),
('core.general.portal', '1'),
('core.general.profile', '1'),
('core.general.search', '1'),
('core.license.email', 'email@domain.com'),
('core.license.key', '6666-6666-6666-6666'),
('core.license.statistics', '1'),
('core.locale.locale', 'auto'),
('core.locale.timezone', 'US/Pacific'),
('core.mail.enabled', '1'),
('core.mail.queueing', '1'),
('core.mail.count', '25'),
('core.secret', 'staticSalt'),
('core.site.title', 'Social Network'),
('core.site.creation', NOW()),
('core.spam.censor', ''),
('core.spam.comment', 0),
('core.spam.contact', 0),
('core.spam.invite', 0),
('core.spam.ipbans', ''),
('core.spam.login', 0),
('core.spam.signup', 0),
('core.tasks.interval', '60'),
('core.tasks.key', ''),
('core.tasks.last', '0'),
('core.tasks.mode', 'curl'),
('core.tasks.pid', ''),
('core.tasks.timeout', '900'),
('core.thumbnails.main.width', '720'),
('core.thumbnails.main.height', '720'),
('core.thumbnails.main.mode', 'resize'),
('core.thumbnails.profile.width', '200'),
('core.thumbnails.profile.height', '400'),
('core.thumbnails.profile.mode', 'resize'),
('core.thumbnails.normal.width', '140'),
('core.thumbnails.normal.height', '160'),
('core.thumbnails.normal.mode', 'resize'),
('core.thumbnails.icon.width', '48'),
('core.thumbnails.icon.height', '48'),
('core.thumbnails.icon.mode', 'crop'),
('core.general.quota', '0'),
('core.general.notificationupdate', 120000)
 * 
 */