<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.4' => array(
    'controllers/AdminManageController.php' => 'Added pagination',
    'externals/scripts/core.js' => 'Added missing translation',
    'Plugin/Core.php' => 'Added missing translation',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Added pagination',
  ),
  '4.0.3' => array(
    'controllers/AdminSettingsController.php' => 'Fixed warning message',
    'controllers/IndexController.php' => 'Removed deprecated code',
    'Bootstrap.php' => 'Removed deprecated code',
    'externals/scripts/core.js' => 'Improved language and localization support',
    'externals/styles/mains.css' => 'Improved RTL support',
    'Plugin/Core.php' => 'Improved language support',
    'views/scripts/index/index.tpl' => 'Improved language support',
    'views/scripts/index/language.tpl' => 'Removed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.2-4.0.3.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/chat.csv' => 'Added missing phrases',
  ),
  '4.0.2' => array(
    'controllers/AdminSettingsController.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
  ),
  '4.0.1' => array(
    'controllers/AdminSettingsController.php' => 'Fixed typo',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>