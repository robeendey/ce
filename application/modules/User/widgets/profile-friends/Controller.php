<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Widget_ProfileFriendsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    //General Friend settings
    $this->view->make_list = Engine_Api::_()->getApi('settings', 'core')->user_friends_lists;

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Multiple friend mode
    $select = $subject->membership()->getMembersSelect();
    $this->view->friends = $friends = $paginator = Zend_Paginator::factory($select);
    $friends->setCurrentPageNumber($this->_getParam('page'));

    // Get stuff
    $ids = array();
    foreach( $friends as $friend )
    {
      $ids[] = $friend->user_id;
    }
    $this->view->friendIds = $ids;

    // Get the items
    $friendUsers = array();
    foreach( Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser )
    {
      $friendUsers[$friendUser->getIdentity()] = $friendUser;
    }
    $this->view->friendUsers = $friendUsers;

    // Get lists if viewing own profile
    if( $viewer->isSelf($subject) ) {
      // Get lists
      $listTable = Engine_Api::_()->getItemTable('user_list');
      $this->view->lists = $lists = $listTable->fetchAll($listTable->select()->where('owner_id = ?', $viewer->getIdentity()));

      $listIds = array();
      foreach( $lists as $list ) {
        $listIds[] = $list->list_id;
      }

      // Build lists by user
      $listItems = array();
      $listsByUser = array();
      if( !empty($listIds) ) {
        $listItemTable = Engine_Api::_()->getItemTable('user_list_item');
        $listItemSelect = $listItemTable->select()
          ->where('list_id IN(?)', $listIds)
          ->where('child_id IN(?)', $ids);
        $listItems = $listItemTable->fetchAll($listItemSelect);
        foreach( $listItems as $listItem ) {
          //$list = $lists->getRowMatching('list_id', $listItem->list_id);
          //$listsByUser[$listItem->child_id][] = $list;
          $listsByUser[$listItem->child_id][] = $listItem->list_id;
        }
      }
      $this->view->listItems = $listItems;
      $this->view->listsByUser = $listsByUser;
    }

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}