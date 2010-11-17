<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Jung
 */
return array(
  '4.0.5' => array(
    'controllers/AdminLevelController.php' => 'Fixed bug preventing switching level',
    'controllers/AlbumController.php' => 'Added registered privacy type',
    'Form/Admin/Settings/Level.php' => 'Added registered privacy type',
    'Form/Album.php' => 'Added registered privacy type',
    'Form/Album/Edit.php' => 'Added registered privacy type',
    'Model/Album.php' => 'Removed redundant search columns',
    'Model/Photo.php' => 'Removed redundant search columns',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support; added registered privacy type',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-level/index.tpl' => 'Fixed bug preventing switching level',
    'views/scripts/album/view.tpl' => 'Fixes rare error with empty titles',
  ),
  '4.0.4' => array(
    'controllers/AlbumController.php' => 'Tweak for message photos',
    'externals/styles/main.css' => 'Improved RTL support',
    'Model/DbTable/Albums.php' => 'Tweak for message photos',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_composePhoto.tpl' => 'Added missing translation',
    'views/scripts/_formButtonCancel.tpl' => 'Removing deprecated code',
    'views/scripts/photo/view.tpl' => 'Added missing translation',
  ),
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Quick navigation uses menu system',
    'Plugin/Menus.php' => 'Better auth handling for menus',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.2-4.0.3.sql' => 'Added',
    'settings/my.sql' => 'Added album menus to the menu editor; quick navigation uses menu system; incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Correct locale date formatting; added missing translation',
    'views/scripts/admin-settings/categories.tpl' => 'Added missing translation',
    'views/scripts/admin-settings/index.tpl' => 'Added missing translation',
    'views/scripts/index/browse.tpl' => 'Quick navigation uses menu system',
    'views/scripts/index/manage.tpl' => 'Quick navigation uses menu system',
    '/application/languages/en/album.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'Api/Core.php' => 'Categories ordered by name',
    'controllers/AdminLevelController.php' => 'Various level settings fixes and enhancements',
    'controllers/AlbumController.php' => 'Fixed activity privacy rebinding problem',
    'controllers/IndexController.php' => 'Fixed problem setting the category in browse',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'Model/Photo.php' => 'Added missing parent::_postDelete(), could have caused issues with orphaned rows',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    '/application/languages/en/album.csv' => 'Fix activity feed translations',
  ),
  '4.0.1' => array(
    'Api/Core.php' => 'Cleanup of temporary files; adjustment for trial',
    'controllers/AlbumController.php' => 'Fixed missing level permissions check',
    'controllers/PhotoController.php' => 'Fixed missing level permissions check; added view count support',
    'Form/Admin/Level.php' => 'Source code formatting',
    'Plugin/Core.php' => 'Query optimization',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.0-4.0.1.sql' => 'Added',
    'settings/my.sql' => 'Added missing view_count and comment_count columns to the engine4_album_photos table',
  ),
) ?>