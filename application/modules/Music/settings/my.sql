
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: my.sql 7562 2010-10-05 22:17:24Z john $
 * @author     Steve
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_playlists`
--

DROP TABLE IF EXISTS `engine4_music_playlists`;
CREATE TABLE IF NOT EXISTS `engine4_music_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(63) NOT NULL default '',
  `description` text NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `owner_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `profile` tinyint(1) NOT NULL default '0',
  `composer` tinyint(1) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `play_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`playlist_id`),
  KEY `creation_date` (`creation_date`),
  KEY `play_count` (`play_count`),
  KEY `owner_id` (`owner_type`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_music_playlist_songs`
--

DROP TABLE IF EXISTS `engine4_music_playlist_songs`;
CREATE TABLE IF NOT EXISTS `engine4_music_playlist_songs` (
  `song_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `play_count` int(11) unsigned NOT NULL default '0',
  `order` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`song_id`),
  KEY (`playlist_id`,`file_id`),
  KEY `play_count` (`play_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_menuitems`
--

INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `order`) VALUES
('core_main_music', 'music', 'Music', '', '{"route":"default","module":"music"}', 'core_main', '', 100),
('core_sitemap_music', 'music', 'Music', '', '{"route":"default","module":"music"}', 'core_sitemap', '', 100),

('core_admin_main_plugins_music', 'music', 'Music', '', '{"route":"admin_default","module":"music","controller":"settings"}', 'core_admin_main_plugins', '', 999),

('music_admin_main_manage', 'music', 'Manage Music', '', '{"route":"admin_default","module":"music","controller":"manage"}', 'music_admin_main', '', 1),
('music_admin_main_settings', 'music', 'Global Settings', '', '{"route":"admin_default","module":"music","controller":"settings"}', 'music_admin_main', '', 2),
('music_admin_main_level', 'music', 'Member Level Settings', '', '{"route":"admin_default","module":"music","controller":"level"}', 'music_admin_main', '', 3),

('authorization_admin_level_music', 'music', 'Music', '', '{"route":"admin_default","module":"music","controller":"level","action":"index"}', 'authorization_admin_level', '', 999)
;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('music', 'Music', 'Music', '4.0.5', 1, 'extra');


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_tasks`
--

INSERT IGNORE INTO `engine4_core_tasks` (`title`, `category`, `module`, `plugin`, `timeout`, `type`, `priority`) VALUES
('Rebuild Music Privacy', 'rebuild_privacy', 'music', 'Music_Plugin_Task_Maintenance_RebuildPrivacy', 0, 'semi-automatic', 20),
('Music Cleanup',	'maintenance', 'music',	'Music_Plugin_Task_Maintenance_Cleanup',	900,	'automatic', 20);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_activity_actiontypes`
--

INSERT IGNORE INTO `engine4_activity_actiontypes` (`type`, `module`, `body`, `enabled`, `displayable`, `attachable`, `commentable`, `shareable`, `is_generated`) VALUES
('music_playlist_new', 'music', '{item:$subject} created a new playlist: {item:$object}', '1', '5', '1', '3', '1', 1),
('comment_playlist',   'music', '{item:$subject} commented on {item:$owner}''s {item:$object:music_playlist}.', 1, 1, 1, 1, 1, 1);


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_authorization_permissions`
--

-- ALL
-- auth_view, auth_comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'auth_view' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'auth_comment' as `name`,
    5 as `value`,
    '["everyone","owner_network","owner_member_member","owner_member","owner"]' as `params`
  FROM `engine4_authorization_levels` WHERE `type` NOT IN('public');

-- ADMIN, MODERATOR
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'edit' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'delete' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'comment' as `name`,
    2 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');

-- USER
-- create, edit, delete, view, comment
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'create' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'edit' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'delete' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'comment' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('user');

-- PUBLIC
-- view
INSERT IGNORE INTO `engine4_authorization_permissions`
  SELECT
    level_id as `level_id`,
    'music_playlist' as `type`,
    'view' as `name`,
    1 as `value`,
    NULL as `params`
  FROM `engine4_authorization_levels` WHERE `type` IN('public');



/*
INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES
(1, 'music_playlist', 'max_filesize', 1, '10000'),
(1, 'music_playlist', 'max_songs', 1, '30'),
(1, 'music_playlist', 'max_storage', 1, '100000'),

(2, 'music_playlist', 'max_filesize', 1, '10000'),
(2, 'music_playlist', 'max_songs', 1, '30'),
(2, 'music_playlist', 'max_storage', 1, '100000'),

(3, 'music_playlist', 'max_filesize', 1, '10000'),
(3, 'music_playlist', 'max_songs', 1, '30'),
(3, 'music_playlist', 'max_storage', 1, '100000'),

(4, 'music_playlist', 'max_filesize', 1, '10000'),
(4, 'music_playlist', 'max_songs', 1, '30'),
(4, 'music_playlist', 'max_storage', 1, '100000'),

(5, 'music_playlist', 'view', 1, NULL);
*/
