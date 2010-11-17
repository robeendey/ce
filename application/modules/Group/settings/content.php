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
    'title' => 'Profile Groups',
    'description' => 'Displays a member\'s groups on their profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-groups',
    'defaultParams' => array(
      'title' => 'Groups',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Group Profile Discussions',
    'description' => 'Displays a group\'s discussions on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-discussions',
  ),
  array(
    'title' => 'Group Profile Info',
    'description' => 'Displays a group\'s info (creation date, member count, leader, officers, etc) on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-info',
  ),
  array(
    'title' => 'Group Profile Members',
    'description' => 'Displays a group\'s members on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-members',
  ),
  array(
    'title' => 'Group Profile Options',
    'description' => 'Displays a menu of actions (edit, report, join, invite, etc) that can be performed on a group on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-options',
  ),
  array(
    'title' => 'Group Profile Photo',
    'description' => 'Displays a group\'s photo on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-photo',
  ),
  array(
    'title' => 'Group Profile Photos',
    'description' => 'Displays a group\'s photos on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-photos',
  ),
  array(
    'title' => 'Group Profile Status',
    'description' => 'Displays a group\'s title on its profile.',
    'category' => 'Group',
    'type' => 'widget',
    'name' => 'group.profile-status',
  ),
  array(
	'title'=> 'Group Profile Events',
        'description' => 'Displays a group\'s events on its profile',
        'category' => 'Group',
        'type' => 'widget',
        'name' => 'group.profile-events'
  )
) ?>