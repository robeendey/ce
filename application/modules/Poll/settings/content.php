<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: content.php 7515 2010-10-01 04:20:24Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Home Poll',
    'description' => 'Displays a single selected poll.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.home-poll',
    'autoEdit' => true,
    //'adminForm' => 'Poll_Form_Admin_Widget_HomePoll',
    'defaultParams' => array(
      'title' => 'Poll',
    ),
  ),
  array(
    'title' => 'Profile Polls',
    'description' => 'Displays a member\'s polls on their profile.',
    'category' => 'Polls',
    'type' => 'widget',
    'name' => 'poll.profile-polls',
    'defaultParams' => array(
      'title' => 'Polls',
      'titleCount' => true,
    ),
  ),
) ?>