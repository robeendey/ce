<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5p1' => array(
    'Model/Action.php' => 'Fixed issue where actions get indexed in global search',
    'Model/Notification.php' => 'Fixed issue where actions get indexed in global search',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.5-4.0.5p1.sql' => 'Added',
    'views/scripts/index/delete.tpl' => 'Fixed issue with incorrect layout after post',
    'widgets/feed/Controller.php' => 'Fixed issue with too many action items being displayed; fixed issue with duplicate items being display when viewing a specific feed item',
  ),
  '4.0.5' => array(
    'Form/Admin/Settings/ActionType.php' => 'Added',
    'Form/Admin/Settings/General.php' => 'Added more feed length options',
    'Model/DbTable/ActionSettings.php' => 'Fixes issues where disabled action types still show in the user settings page',
    'Model/DbTable/ActionTypes.php' => 'Fixes issues where disabled action types still show in the user settings page',
    'Model/DbTable/Notifications.php' => 'Added more variables for mail templates',
    'Model/Helper/Item.php' => 'Added handling for missing href',
    'Model/Helper/Translate.php' => 'Added no translate bit',
    'Model/Notification.php' => 'Typo in error message',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support',
    'View/Helper/ActivityLoop.php' => 'Can now accept an array in addition to a rowset',
    'controllers/AdminSettingsController.php' => 'Added action for configuring action types',
    'controllers/IndexController.php' => 'Added auth for composer plugins; fixed context switch issues',
    'externals/images/nophoto_action_thumb_icon.png' => 'Added',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_activityText.tpl' => 'Moved some processing of actions to controller; fixed some errors when items are missing',
    'views/scripts/admin-settings/types.tpl' => 'Added',
    'views/scripts/notifications/index.tpl' => 'Fixed incorrect url that would prevent view more from working',
    'widgets/feed/Controller.php' => 'Improved user limit handling, should display the full feed length in most cases now',
    'widgets/feed/index.tpl' => 'Fixes composer javascript when feed is empty',
  ),
  '4.0.4' => array(
    'controllers/AdminSettingsController.php' => 'Fixed problem with disabling or enabling activity feed item types',
    'controllers/NotificationsController.php' => 'Moved pulldown update here',
    'externals/styles/main.css' => 'Improved RTL support',
    'Form/Admin/Settings/General.php' => 'Fixed problem with disabling or enabling activity feed item types',
    'Model/Helper/Body.php' => 'Added container around post body for future improved RTL support',
    'Plugin/Core.php' => 'Fixed issues with privacy in the feed when content is hidden from the public by admin settings',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_formActivityButton.tpl' => 'Removed deprecated code',
    'views/scripts/notifications/pulldown.tpl' => 'Moved pulldown update here',
    'views/scripts/widget/*' => 'Removing deprecated code',
    'widgets/feed/index.tpl' => 'Added missing translation; fixed smoothbox binding on view more; fixed incorrect inclusion of javascript files',
    '/application/languages/en/activity.csv' => 'Added phrases',
  ),
  '4.0.3' => array(
    'Model/DbTable/NotificationTypes.php' => 'Fixed bug with missing notification emails',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/activity.csv' => 'Added phrases',
  ),
  '4.0.2p1' => array(
    'Model/Helper/Item.php' => 'Fixed pluralization of updates text',
    'settings/manifest.php' => 'Incremented version',
  ),
  '4.0.2' => array(
    'controllers/IndexController.php' => 'Added missing authorization checks',
    'Model/DbTable/Actions.php' => 'Fixed bad IN clauses in query',
    'Model/Helper/Item.php' => 'Adds translation of item text in update notifications',
    'Plugin/Core.php' => 'Fixed several privacy issues',
    'settings/manifest.php' => 'Incremented version',
    'views/scripts/_activityText.tpl' => 'Added missing authorization checks',
    '/application/languages/en/activity.csv' => 'Adds translation of item text in update notifications',
  ),
  '4.0.1' => array(
    'Model/DbTable/Notifications.php' => 'Fixes problem with notifications from disabled modules',
    'Plugin/Core.php' => 'Fixes problem with properly detecting the page subject and handles items without parents properly',
    'settings/manifest.php' => 'Incremented version',
    'views/scripts/notifications/index.tpl' => 'Fixes problem with notifications from disabled modules',
    'widgets/list-requests/index.tpl' => 'Fixes problem with notifications from disabled modules',
  ),
) ?>