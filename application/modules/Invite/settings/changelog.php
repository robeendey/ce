<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Steve
 */
return array(
  '4.0.3' => array(
    'controllers/IndexController.php' => 'Added missing beginTransaction',
    'Plugin/Signup.php' => 'Friendship requests are not sent if friendships are disabled',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/invite.csv' => 'Added missing phrases',
  ),
  '4.0.2' => array(
    'Api/Core.php' => 'Removed deprecated code',
    'controllers/IndexController.php' => 'Uses common method',
    'controllers/SignupController.php' => 'Refactored and improved',
    'Form/Invite.php' => 'Updated email params',
    'Model/DbTable/Invites.php' => 'Added common sendInvites method; fixes incorrect link in invite message',
    'Plugin/Signup.php' => 'Refactored and improved',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/invite.csv' => 'Added missing phrases',
  ),
  '4.0.1' => array(
    'controllers/IndexController.php' => 'Users could send invites even if disabled',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>