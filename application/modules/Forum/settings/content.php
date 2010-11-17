<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Profile Forum Topics',
    'description' => 'Displays a member\'s forum topics on their profile.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'forum.profile-forum-topics',
    'defaultParams' => array(
      'title' => 'Forum Topics',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Forum Posts',
    'description' => 'Displays a member\'s forum posts on their profile.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'forum.profile-forum-posts',
    'defaultParams' => array(
      'title' => 'Forum Posts',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Recent Forum Topics',
    'description' => 'Displays recently created forum topics.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'forum.list-recent-topics',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Forum Topics',
    ),
  ),
  array(
    'title' => 'Recent Forum Posts',
    'description' => 'Displays recent forum posts.',
    'category' => 'Forum',
    'type' => 'widget',
    'name' => 'forum.list-recent-posts',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Forum Posts',
    ),
  ),
) ?>