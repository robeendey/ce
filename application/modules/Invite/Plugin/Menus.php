<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 7322 2010-09-09 05:05:22Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Invite_Plugin_Menus
{
  public function canInvite()
  {
    // Check if admins only
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('user.signup.inviteonly') == 1 ) {
      return (bool) Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view');
    } else {
      return (bool) Engine_Api::_()->user()->getViewer()->getIdentity();
    }
  }
}