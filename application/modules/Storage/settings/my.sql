
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: my.sql 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */


-- --------------------------------------------------------

--
-- Table structure for table `engine4_storage_chunks`
--

DROP TABLE IF EXISTS `engine4_storage_chunks`;
CREATE TABLE IF NOT EXISTS `engine4_storage_chunks` (
  `chunk_id` bigint(20) unsigned NOT NULL auto_increment,
  `file_id` int(11) unsigned NOT NULL,
  `data` blob NOT NULL,
  PRIMARY KEY  (`chunk_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM;


-- --------------------------------------------------------

--
-- Table structure for table `engine4_storage_files`
--

DROP TABLE IF EXISTS `engine4_storage_files`;
CREATE TABLE `engine4_storage_files` (
  `file_id` int(11) unsigned NOT NULL auto_increment,
  `parent_file_id` int(11) unsigned NULL,
  `type` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NULL,

  `parent_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `parent_id` int(11) unsigned default NULL,
  `user_id` int(11) unsigned default NULL,
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  
  `storage_type` varchar(32) NOT NULL,
  `storage_path` varchar(255) NOT NULL,
  `extension` varchar(8) NOT NULL,
  `name` varchar(255) default NULL,
  `mime_major` varchar(64) NOT NULL,
  `mime_minor` varchar(64) NOT NULL,
  `size` bigint(20) unsigned NOT NULL,
  `hash` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,

  PRIMARY KEY  (`file_id`),
  UNIQUE KEY  (`parent_file_id`,`type`),
  KEY `PARENT` (`parent_type`,`parent_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;


-- --------------------------------------------------------

--
-- Dumping data for table `engine4_core_modules`
--

INSERT INTO `engine4_core_modules` (`name`, `title`, `description`, `version`, `enabled`, `type`) VALUES
('storage', 'Storage', 'Storage', '4.0.4', 1, 'core');

