<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7433 2010-09-20 23:36:28Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Chat
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Chat_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();
    $viewer = Engine_Api::_()->user()->getViewer();
    
    if( $view instanceof Zend_View && $viewer->getIdentity() ) {

      // Check if enabled
      $enabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.im.enabled', false);
      if( !$enabled ) {
        return;
      }
      
      $identity = sprintf('%d', $viewer->getIdentity());
      $delay = Engine_Api::_()->getApi('settings', 'core')->getSetting('chat.general.delay', '5000');
      
      $script = <<<EOF
  var chatHandler;
  en4.core.runonce.add(function() {
    try {
      chatHandler = new ChatHandler({
        'baseUrl' : en4.core.baseUrl,
        'basePath' : en4.core.basePath,
        'identity' : {$identity},
        'enableChat' : false,
        'delay' : {$delay}
      });

      chatHandler.start();
      window._chatHandler = chatHandler;
    } catch( e ) {
      //if( \$type(console) ) console.log(e);
    }
  });
EOF;
      
      $view->headScript()
        ->appendFile('application/modules/Chat/externals/scripts/core.js')
        ->appendScript($script);

      $view->headTranslate(array(
        'The chat room has been disabled by the site admin.', 'Browse Chatrooms',
        'You are sending messages too quickly - please wait a few seconds and try again.',
        '%1$s has joined the room.', '%1$s has left the room.', 'Settings',
        'Friends Online', 'None of your friends are online.', 'Go Offline',
        'Open Chat', 'General Chat', 'Introduce Yourself', '%1$s person',
        'You',
      ));
    }
  }

  public function onRenderLayoutAdminDefault($event)
  {
    //return $this->onRenderLayoutDefault($event);
  }
}