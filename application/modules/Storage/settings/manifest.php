<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'storage',
    'version' => '4.0.4',
    'revision' => '$Revision: 7562 $',
    'path' => 'application/modules/Storage',
    'repository' => 'socialengine.net',
    'title' => 'Storage',
    'description' => 'Storage',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.0.5',
      ),
    ),
    'tests' => array(
      array(
        'type' => 'MysqlEngine',
        'name' => 'MySQL MyISAM Storage Engine',
        'engine' => 'myisam',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       //'enable',
       //'disable',
     ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Module',
      'priority' => 5000,
    ),
    'directories' => array(
      'application/modules/Storage',
    ),
    'files' => array(
      'application/languages/en/storage.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Storage_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'storage_file',
  )
  // Routes --------------------------------------------------------------------
) ?>