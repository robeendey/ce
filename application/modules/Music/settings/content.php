<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 7518 2010-10-01 09:27:40Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Home Playlist',
    'description' => 'Displays a single selected playlist.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.home-playlist',
    'autoEdit' => true,
    //'adminForm' => 'Music_Form_Admin_Widget_HomePlaylist',
    'defaultParams' => array(
      'title' => 'Playlist',
    ),
  ),
  array(
    'title' => 'Profile Music',
    'description' => 'Displays a member\'s music on their profile.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.profile-music',
    'defaultParams' => array(
      'title' => 'Music',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Player',
    'description' => 'Displays a flash player that plays the music the member has selected to play on their profile.',
    'category' => 'Music',
    'type' => 'widget',
    'name' => 'music.profile-player',
  ),
) ?>