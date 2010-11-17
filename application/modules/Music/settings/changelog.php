<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5' => array(
    'controllers/AdminLevelController.php' => 'Fixed bug preventing switching level; emoved deprecated code',
    'controllers/AdminManageController.php' => 'Added admin suggest for widget form',
    'controllers/IndexController.php' => 'Fixed issue preventing admin from editing playlist; removed deprecated context switch code',
    'externals/scripts/composer_music.js' => 'Fixed composer preview player',
    'externals/scripts/core.js' => 'Added separate CSS classes for enable/disable profile playlist',
    'externals/styles/admin/main.css' => 'Added',
    'externals/styles/main.css' => 'Added separate CSS classes for enable/disable profile playlist',
    'Form/Admin/Settings/Level.php' => 'Added registered privacy type',
    'Form/Admin/Widget/HomePlaylist.php' => 'Added',
    'Form/Create.php' => 'Added registered privacy type; added missing .jpeg extension to allowed file types',
    'Model/Playlist.php' => 'Minor performance improvement',
    'Model/PlaylistSong.php' => 'Added missing translation; fixed issue with exceptions while deleting files',
    'Model/Song.php' => 'Compat for search index changes',
    'Plugin/Task/Maintenance/Cleanup.php' => 'Added',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support',
    'settings/changelog.php' => 'Added',
    'settings/content.php' => 'Added widget',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_FancyUpload.tpl' => 'Logging is enabled in development mode',
    'views/scripts/_Player.tpl' => 'Now uses javascript translation api',
    'views/scripts/admin-level/index.tpl' => 'Fixed bug preventing changing level',
    'views/scripts/index/browse.tpl' => 'Added separate CSS classes for enable/disable profile playlist',
    'views/scripts/index/manage.tpl' => 'Added separate CSS classes for enable/disable profile playlist',
    'views/scripts/index/playlist.tpl' => 'Added separate CSS classes for enable/disable profile playlist',
    'widgets/home-playlist/admin.tpl' => 'Added',
    'widgets/home-playlist/Controller.php' => 'Added',
    'widgets/home-playlist/index.tpl' => 'Added',
  ),
  '4.0.4' => array(
    'externals/soundmanager/script/soundmanager2.js' => 'Improved RTL support',
    'externals/styles/main.css' => 'Improved RTL support',
    'Form/Playlist.php' => 'Removing deprecated code',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_composeMusic.tpl' => 'Added missing translation',
    'views/scripts/_FancyUpload.tpl' => 'Added missing translation',
    'views/scripts/_formButtonCancel.tpl' => 'Removing deprecated code',
    'views/scripts/index/manage.tpl' => 'Added missing translation',
  ),
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Fixes for activity privacy problems, default playlists per page increased',
    'Form/Create.php' => 'Cleanup',
    'Model/PlaylistSong.php' => 'Fixes song deletion when deleting playlist',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/music.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'controllers/AdminLevelController.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/index/manage.tpl' => 'Missing translations',
  ),
  '4.0.1' => array(
    'controllers/IndexController.php' => 'Default playlists default to searchable and fixed public permissions',
    'Form/Admin/Level.php' => 'Fixed problem in level select',
    'Model/Playlist.php' => 'Better cleanup of temporary files',
    'Plugin/Core.php' => 'Query optimization',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>