<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5' => array(
    'Api/Core.php' => 'Added new filtering options; removing deprecated code',
    'controllers/AdminManageController.php' => 'Added admin suggest action for widget edit form',
    'controllers/IndexController.php' => 'Various auth, paginatiom, filtering improvements; added menus to menu editor',
    'externals/images/nophoto_poll_thumb_icon.png' => 'Added',
    'externals/scripts/core.js' => 'Added',
    'externals/styles/admin/main.css' => 'Added',
    'externals/styles/main.css' => 'Added styles for new widget and feed rich content',
    'Form/Admin/Settings/Level.php' => 'Added registered privacy type',
    'Form/Admin/Widget/HomePoll.php' => 'Added',
    'Form/Create.php' => 'Added registered privacy type',
    'Form/Index/Search.php' => 'Removed',
    'Form/Index/Vote.php' => 'Removed',
    'Form/Search.php' => 'Added',
    'Model/Poll.php' => 'Added search indexing of poll option labels; added poll display/voting in the feed; performance improvements; removed deprecated code',
    'Plugin/Core.php' => 'Removed deprecated code',
    'Plugin/Menus.php' => 'Added',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support',
    'settings/changelog.php' => 'Added',
    'settings/content.php' => 'Added widget',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_poll.tpl' => 'Added',
    'views/scripts/admin-manage/index.tpl' => 'Added title truncation',
    'views/scripts/index/browse.tpl' => 'Improved pagination and filtering; added menus to menu editor',
    'views/scripts/index/manage.tpl' => 'Improved pagination and filtering; added menus to menu editor',
    'views/scripts/index/view.tpl' => 'Moved a lot of code to core.js and _poll.tpl',
    'widgets/home-poll/admin.tpl' => 'Added',
    'widgets/home-poll/Controller.php' => 'Added',
    'widgets/home-poll/index.tpl' => 'Added',
    'widgets/profile-polls/index.tpl' => 'Removed deprecated route',
    '/application/languages/en/poll.csv' => 'Added missing phrases',
  ),
  '4.0.4' => array(
    'externals/styles/main.css' => 'Improved RTL support',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/index/manage.tpl' => 'Added missing translation',
    '/application/languages/en/poll.csv' => 'Added missing phrases',
  ),
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Fixed activity privacy bug',
    'Form/Create.php' => 'Added',
    'Form/Edit.php' => 'Added',
    'Form/Index/Create.php' => 'Moved',
    'Form/Index/Edit.php' => 'Moved',
    'Model/Poll.php' => 'Fixed search indexing bug',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Added correct locale date format',
    'views/scripts/index/create.tpl' => 'Added re-population of options on failed validation',
    '/application/languages/en/poll.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'controllers/AdminSettingsController.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
  ),
  '4.0.1' => array(
    'controllers/AdminSettingsController.php' => 'Fixed problem in level select',
    'controllers/IndexController.php' => 'Fixed public permissions',
    'Plugin/Core.php' => 'Query optimization',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.0-4.0.1.sql' => 'Added',
    'settings/my.sql' => 'Added missing search, view_count, comment_count columns to the engine4_poll_polls table',
  ),
) ?>