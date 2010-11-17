<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Jung
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'classified',
    'version' => '4.0.5',
    'revision' => '$Revision: 7562 $',
    'path' => 'application/modules/Classified',
    'repository' => 'socialengine.net',
    'title' => 'Classifieds',
    'description' => 'Classifieds',
    'author' => 'Webligo Developments',
    'changeLog' => 'settings/changelog.php',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.0.5',
      ),
    ),
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
     ),
    'callback' => array(
      'path' => 'application/modules/Classified/settings/install.php',
      'class' => 'Classified_Installer',
    ),
    'directories' => array(
      'application/modules/Classified',
    ),
    'files' => array(
      'application/languages/en/classified.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Classified_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Classified_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'classified',
    'classified_album',
    'classified_photo'
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'classified_extended' => array(
      'route' => 'classifieds/:controller/:action/*',
      'defaults' => array(
        'module' => 'classified',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      ),
    ),
    'classified_general' => array(
      'route' => 'classifieds/:action/*',
      'defaults' => array(
        'module' => 'classified',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'action' => '(browse|manage|create)',
      ),
    ),
    'classified_specific' => array(
      'route' => 'classifieds/:action/:classified_id/*',
      'defaults' => array(
        'module' => 'classified',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'classified_id' => '\d+',
        'action' => '(delete|edit|close|success)',
      ),
    ),


    // Public
    'classified_view' => array(
      'route' => 'classifieds/:user_id/*',
      'defaults' => array(
        'module' => 'classified',
        'controller' => 'index',
        'action' => 'list'
      ),
      'reqs' => array(
        'user_id' => '\d+'
      )
    ),
    'classified_entry_view' => array(
      'route' => 'classifieds/:user_id/:classified_id/:slug',
      'defaults' => array(
        'module' => 'classified',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'classified_id' => '\d+'
      )
    ),
  ),
);
