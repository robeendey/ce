<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5' => array(
    'controllers/MessagesController.php' => 'Removed deprecated code',
    'Model/Conversation.php' => 'Different',
    'Model/Message.php' => 'Compat for search indexing changes',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/messages.csv' => 'Added missing phrases',
  ),
  '4.0.4' => array(
    'controllers/MessagesController.php' => 'Removed deprecated code',
    'externals/styles/main.css' => 'Improved RTL support',
    'Model/DbTable/Conversations.php' => 'Added title and user identity',
    'Model/Conversation.php' => 'Removed title from replies for now (it\'s not being used for replies and the auto "Re:" was not getting translated)',
    'views/scripts/messages/compose.tpl' => 'Improved RTL support',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/messages/inbox.tpl' => 'Added missing translation; fixed conversation title',
    'views/scripts/messages/outbox.tpl' => 'Added missing translation; fixed conversation title',
    'views/scripts/messages/view.tpl' => 'Added missing translation; fixed conversation title',
    '/application/languages/en/messages.csv' => 'Added phrases',
  ),
  '4.0.3' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.2-4.0.3.sql' => 'Added',
    'settings/my.sql' => 'Incremented version; added email notification template for new message',
    '/application/languages/en/messages.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/messages/inbox.tpl' => 'Delete Selected is now translated',
  ),
  '4.0.1' => array(
    'controllers/AdminSettingsController.php' => 'Fixed problem in level select',
    'controllers/MessagesController.php' => 'Changed json_encode to Zend_Json::encode',
    'settings/manifest.php' => 'Incremented version',
  ),
) ?>