<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Menus.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Blog_Plugin_Menus
{
  public function canCreateBlogs()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create blogs
    if( !Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function canViewBlogs()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    // Must be able to view blogs
    if( !Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'view') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_BlogQuickStyle($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $request = Zend_Controller_Front::getInstance()->getRequest();
    
    if( $request->getParam('module') != 'blog' || $request->getParam('action') != 'manage' ) {
      return false;
    }
    
    // Must be able to style blogs
    if( !Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'style') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_BlogGutterList($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject instanceof User_Model_User ) {
      $user_id = $subject->getIdentity();
    } else if( $subject instanceof Blog_Model_Blog ) {
      $user_id = $subject->owner_id;
    } else {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['user_id'] = $user_id;
    return $params;
  }

  public function onMenuInitialize_BlogGutterCreate($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $blog = Engine_Api::_()->core()->getSubject('blog');

    if( !$blog->isOwner($viewer) ) {
      return false;
    }

    if( !Engine_Api::_()->authorization()->isAllowed('blog', $viewer, 'create') ) {
      return false;
    }

    return true;
  }

  public function onMenuInitialize_BlogGutterEdit($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $blog = Engine_Api::_()->core()->getSubject('blog');

    if( !$blog->authorization()->isAllowed($viewer, 'edit') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['blog_id'] = $blog->getIdentity();
    return $params;
  }

  public function onMenuInitialize_BlogGutterDelete($row)
  {
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $blog = Engine_Api::_()->core()->getSubject('blog');

    if( !$blog->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    // Modify params
    $params = $row->params;
    $params['params']['blog_id'] = $blog->getIdentity();
    return $params;
  }
}