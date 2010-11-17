<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Jung
 */
return array(
  '4.0.5' => array(
    'Api/Core.php' => 'Fixed video tag searches causing exceptions; fixed issue with duplicate results when viewing a tag',
    'controllers/AdminSettingsController.php' => 'Fixed problems with open_basedir and ffmpeg path verification/detection',
    'controllers/IndexController.php' => 'Added registered privacy type',
    'Form/Admin/Settings/Level.php' => 'Added registered privacy type',
    'Form/Edit.php' => 'Added registered privacy type',
    'Form/Video.php' => 'Added registered privacy type',
    'Model/Video.php' => 'Tweak to slug handling',
    'Plugin/Task/Encode.php' => 'Added notifications on failed encoding; fixed problems with open_basedir and ffmpeg path verification/detection; code formatting',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support',
    'settings/changelog.php' => 'Added',
    'settings/content.php' => 'Code formatting',
    'settings/install.php' => 'Fixed problems with open_basedir and ffmpeg path verification/detection',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Fixed missing category in encode task',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/index/create.tpl' => 'Fixed issue with selecting tags from autosuggest',
    '/application/languages/en/video.csv' => 'Added missing phrases',
  ),
  '4.0.4' => array(
    'Api/Core.php' => 'Added the ability to search browse videos by search phrase',
    'controllers/AdminSettingsController.php' => 'Fixed warning message about ffmpeg when open_basedir enabled',
    'controllers/IndexController.php' => 'Fixed warning messages; added the ability to search browse videos by search phrase',
    'externals/styles/main.css' => 'Improved RTL support',
    'Form/Edit.php' => 'Removing deprecated code; fixed issue with incorrect auth checking',
    'Form/Search.php' => 'Added the ability to search browse videos by search phrase',
    'Plugin/Task/Encode.php' => 'Added progress reporting',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added; fixed typo',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added; added code to address typo',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_FancyUpload.tpl' => 'Added missing translation',
    'views/scripts/_formButtonCancel.tpl' => 'Removing deprecated code',
    'views/scripts/index/browse.tpl' => 'Improved RTL support',
    'views/scripts/index/view.tpl' => 'Improved RTL support',
    'views/scripts/upload/upload.tpl' => 'Added missing translation',
    'widgets/profile-videos/index.tpl' => 'Improved RTL support',
    '/application/languages/en/video.csv' => 'Added phrases',
  ),
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Fixed activity privacy bug; fixed quote handling bug',
    'controllers/AdminSettingsController.php' => 'Show error message if FFMPEG path is invalid',
    'Form/Video.php' => 'Hides the privacy setting if there are no privacy set',
    'Plugin/Task/Encode.php' => 'Fixed activity privacy bug; Passes owner_id to storage system; Sets video to processing only after FFMPEG check passes',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_composeVideo.tpl' => 'Added missing translation',
    'views/scripts/admin-manage/index.tpl' => 'Added correct locale date format',
    'views/scripts/index/create.tpl' => 'Fixed unlimited quota bug',
    'views/scripts/index/manage.tpl' => 'Fixed unlimited quota bug',
    'views/scripts/index/view.tpl' => 'Added missing translation',
    'widgets/list-recent-videos/Controller.php' => 'No longer shows videos that failed or have not finished encoding',
    '/application/languages/en/video.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'Api/Core.php' => 'Categories ordered by name',
    'controllers/AdminSettingsController.php' => 'Various level settings fixes and enhancements',
    'controllers/IndexController.php' => 'Filter form now accepts GET requests',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'Plugin/Task/Encode.php' => 'Added Ffmpeg validation prior to running encode task.',
    'settings/install.php' => 'Checks for ffmpeg binary on install/upgrade',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/admin-manage/index.tpl' => 'Uses displayname instead of username',
    'views/scripts/index/browse.tpl' => 'Pagination control keeps filter form params',
  ),
  '4.0.1' => array(
    'Api/Core.php' => 'Adjustments for trial',
    'controllers/AdminSettingsController.php' => 'Fixed problem in level select',
    'controllers/IndexController.php' => 'Better cleanup of temporary files and fixed public permissions',
    'controllers/UploadController.php' => 'Fixed missing level permission check',
    'Plugin/Core.php' => 'Query optimization',
    'Plugin/Task/Encode.php' => 'Better cleanup of temporary files',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.0-4.0.1.sql' => 'Added',
    'settings/my.sql' => 'Added comment_count column to engine4_video_videos table',
  ),
) ?>