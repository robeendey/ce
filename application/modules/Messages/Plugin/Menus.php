<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Menus.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Messages_Plugin_Menus
{
  // core_mini
  
  public function onMenuInitialize_CoreMiniMessages($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);
    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl() . '/';

    return array(
      'label' => Zend_Registry::get('Zend_Translate')->_($row->label) . ( $message_count ? ' (' . $message_count .')' : '' ),
      'route' => 'messages_general',
      'params' => array(
        'action' => 'inbox'
      )
    );
  }



  // user_profile

  public function onMenuInitialize_UserProfileMessage($row)
  {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) )
    {
      return false;
    }
    
    // @todo check if can send message
    return array(
      'label' => "Send Message",
      'icon' => 'application/modules/Messages/externals/images/send.png',
      'route' => 'messages_general',
      'params' => array(
        'action' => 'compose',
         'to' => $subject->getIdentity()
      ),
    );
  }
}
