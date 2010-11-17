<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5' => array(
    'Controller/Action/Helper/RequireAuth.php' => 'Added support for nested auth actions',
    'Form/Admin/Level/Edit.php' => 'Code formatting',
    'Model/DbTable/Allow.php' => 'Fixes issue with permissions granted to specific resources',
    'Model/DbTable/Permissions.php' => 'Compat for logging modifications',
    'Model/Level.php' => 'Added support for granting authorization to members (for forums)',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.4' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added to purge levels from search index',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.3' => array(
    'Model/Level.php' => 'Code optimizations; fixed nested transaction error with pdo_mysql',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'controllers/AdminLevelController.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level/Abstract.php' => 'Various level settings fixes and enhancements',
    'Form/Admin/Level/Create.php' => 'Various level settings fixes and enhancements; added level type',
    'Form/Admin/Level/Edit.php' => 'Various level settings fixes and enhancements',
    'Model/DbTable/Allow.php' => 'Added auth type for members invited to a group or event',
    'Model/DbTable/Permissions.php' => 'Fixes issue when an empty array is passed to getAllowed()',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/admin-level/index.tpl' => 'Added column for level type; added missing translation',
  ),
  '4.0.1' => array(
    'Form/Admin/Level/Edit.php' => 'Storage quotas are now level-based',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>