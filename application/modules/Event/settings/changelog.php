<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Sami
 */
return array(
  '4.0.5' => array(
    'controllers/IndexController.php' => 'Fixed errors caused by disabled action types',
    'controllers/MemberController.php' => 'Added support for missing action and notification types; join form now shows RSVP options',
    'controllers/PostController.php' => 'Improved auth handling',
    'controllers/TopicController.php' => 'Added topic watching support',
    'controllers/WidgetController.php' => 'Added notification request support',
    'externals/images/nophoto_event_thumb_icon.png' => 'Changed',
    'externals/images/unwatch.png' => 'Added',
    'externals/images/watch.png' => 'Added',
    'externals/styles/main.css' => 'Added styles; fixed style issue in topic view list',
    'Form/Create.php' => 'Added missing .jpeg extension to allowed file types',
    'Form/Edit.php' => 'Added missing .jpeg extension to allowed file types',
    'Form/Member/Join.php' => 'Removed token',
    'Form/Member/Reject.php' => 'Removed token',
    'Form/Post/Create.php' => 'Added topic watching support',
    'Form/Topic/Create.php' => 'Added topic watching support',
    'Model/DbTable/TopicWatches.php' => 'Added',
    'Model/Photo.php' => 'Improved checks on file deletion',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support',
    'settings/changelog.php' => 'Added',
    'settings/content.php' => 'Added support for configuring number of events displayed on the Upcoming Events widget',
    'settings/install.php' => 'Code formatting',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Fixed link to owner\'s profile',
    'views/scripts/index/browse.tpl' => 'Fixed issue with extra parameter in url',
    'views/scripts/index/manage.tpl' => 'Fixed issue with extra parameter in url',
    'views/scripts/topic/view.tpl' => 'Added topic watching support; fixed issue with quoting posts conataing quotes',
    'views/scripts/widget/request-event.tpl' => 'Added',
    'widgets/home-upcoming/Controller.php' => 'Change filter from start time to end time to display ongoing events',
    'widgets/home-upcoming/index.tpl' => 'Displays ongoing events',
    '/application/languages/en/event.csv' => 'Added missing phrases',
  ),
  '4.0.4' => array(
    'controllers/EventController.php' => 'Fixes timezone issues; fixed category ordering',
    'controllers/IndexController.php' => 'Fixes timezone issues; fixed category ordering',
    'controllers/MemberController.php' => 'Added RSVP select when joining an event',
    'externals/styles/main.css' => 'Improved RTL support',
    'Form/Rsvp.php' => 'Code cleanup',
    'Form/Member/*' => 'Fixes smoothbox problems on failed form validation',
    'Model/Event.php' => 'Added awaiting reply RSVP type',
    'Plugin/Core.php' => 'Fixed issues with privacy in the feed when content is hidden from the public by admin settings',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/install.php' => 'Group page should not be considered a custom page',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added; fixed custom page setting',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/widget' => 'Removed deprecated code',
    'views/scripts/index/browse.tpl' => 'Improved localization support; fixes hiding of filter form when options return no results',
    'views/scripts/index/create.tpl' => 'Removed deprecated code',
    'views/scripts/index/manage.tpl' => 'Improved localization support',
    'views/scripts/photo/view.tpl' => 'Added missing translation',
    'widgets/profile-info/index.tpl' => 'Improved localization support',
    'widgets/profile-members/index.tpl' => 'Added awaiting reply RSVP type',
    '/application/languages/en/event.csv' => 'Added missing phrases',
  ),
  '4.0.3' => array(
    'controllers/AdminSettingsController.php' => 'Tweak for public level',
    'controllers/IndexController.php' => 'Ordering categories by name',
    'externals/styles/main.css' => 'Styles for RSVP in member list',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Added correct locale date format',
    'widgets/profile-members/index.tpl' => 'Added display of member RSVP',
    '/application/languages/en/event.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'Api/Core.php' => 'Categories ordered by name',
    'controllers/AdminSettingsController.php' => 'Various level settings fixes and enhancements',
    'controllers/EventController.php' => 'Various level settings fixes and enhancements',
    'controllers/IndexController.php' => 'Various level settings fixes and enhancements',
    'Form/Create.php' => 'Various level settings fixes and enhancements',
    'Form/Edit.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'Plugin/Core.php' => 'Added activity stream index type',
    'settings/content.php' => 'Added configuration options for Upcoming Events widget',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'widgets/home-upcoming/Controller.php' => 'Added configuration options for Upcoming Events widget',
    'widgets/profile-events/Controller.php' => 'Added parent check',
  ),
  '4.0.1' => array(
    'Api/Core.php' => 'Better cleanup of temporary files',
    'controllers/AdminSettingsController.php' => 'Fixed problem in level select',
    'controllers/EventController.php' => 'Added level permission for styles',
    'controllers/PhotoController.php' => 'Added view count support',
    'controllers/TopicController.php' => 'Added view count support',
    'Form/Admin/Level.php' => 'Added level permission for styles',
    'Model/Event.php' => 'Better cleanup of temporary files',
    'Plugin/Core.php' => 'Query optimization; fixed typo that would cause problem on user deletion',
    'Plugin/Menus.php' => 'Added level permission for styles',
    'settings/manifest.php' => 'Incremented version; fixed typo',
    'settings/my-upgrade-4.0.0-4.0.1.sql' => 'Added',
    'settings/my.sql' => 'Added view_count and comment_count columns to engine4_event_photos table; added view_count column to engine4_event_topics table; added default permissions for style permission',
  ),
) ?>