<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Sami
 */
return array(
  '4.0.5p1' => array(
    'Model/Network.php' => 'Fixed issue where networks would get indexed in global search',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.5-4.0.5p1.sql' => 'Added',
  ),
  '4.0.5' => array(
    'externals/images/nophoto_network_thumb_icon.png' => 'Added',
    'Plugin/Task/Maintenance/RebuildMembership.php' => 'Added idle support',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/admin-manage/delete.tpl' => 'Tweak for parent refresh',
  ),
  '4.0.4' => array(
    'externals/styles/main.css' => 'Improved RTL support',
    'Model/Network.php' => 'Fixes to improve memory leak issue in network admin panel page',
    'Plugin/Task/Maintenance/RebuildMembership.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.3' => array(
    'Model/Network.php' => 'Fixed multi checkbox and multi select field support',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/network.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'controllers/NetworkController.php' => 'Fixed missing check for invisible networks',
    'settings/manifest.php' => 'Incremented version',
  ),
  '4.0.1' => array(
    'controllers/AdminManageController.php' => 'Added missing pagination',
    'settings/manifest.php' => 'Incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Added missing pagination',
    'network.csv' => 'Repair to invalid language string.',
  ),
) ?>