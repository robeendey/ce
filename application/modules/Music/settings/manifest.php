<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'music',
    'version' => '4.0.5',
    'revision' => '$Revision: 7562 $',
    'path' => 'application/modules/Music',
    'repository' => 'socialengine.net',
    'title' => 'Music',
    'description' => 'Music',
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
      'path' => 'application/modules/Music/settings/install.php',
      'class' => 'Music_Installer',
    ),
    'directories' => array(
      'application/modules/Music',
    ),
    'files' => array(
      'application/languages/en/music.csv',
    ),
  ),
  // Compose -------------------------------------------------------------------
  'compose' => array(
    array('_composeMusic.tpl', 'music'),
  ),
  'composer' => array(
    'music' => array(
      'script' => array('_composeMusic.tpl', 'music'),
      'plugin' => 'Music_Plugin_Composer',
      'auth' => array('music_playlist', 'create'),
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onStatistics',
      'resource' => 'Music_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Music_Plugin_Core',
    ),
  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'music_playlist',
    'music_playlist_song',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'music_browse' => array(
      'route' => 'music/:page/*',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'browse',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'music_manage' => array(
      'route' => 'music/manage/:page/*',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'manage',
        'page' => 1,
      ),
      'reqs' => array(
        'page' => '\d+'
      ),
    ),
    'music_edit' => array(
      'route' => 'music/edit/:playlist_id',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'edit',
      ),
      'reqs' => array(
        'playlist_id' => '\d+',
      )
    ),
    'music_create' => array(
      'route' => 'music/create',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'create',
      ),
    ),
    'music_search' => array(
      'route' => 'music/search',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'search',
      )
    ),
    'music_playlist_append' => array(
      'route' => 'music/playlist/append/*',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'playlist-append',
      ),
    ),
    'music_playlist' => array(
      'route' => 'music/playlist/:playlist_id/*',
      'defaults' => array(
        'module' => 'music',
        'controller' => 'index',
        'action' => 'playlist',
        'playlist_id' => 0,
      ),
      'reqs' => array(
        'playlist_id' => '\d+',
      ),
    ),
  ),
) ?>