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
class User_Widget_ProfileFriendsCommonController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    // Just remove the title decorator
    $this->getElement()->removeDecorator('Title');

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    //if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
    //  return $this->setNoRender();
    //}

    // If no viewer or viewer==subject, don't display
    if( !$viewer->getIdentity() || $viewer->isSelf($subject) ) {
      return $this->setNoRender();
    }

    // Diff friends
    $friendsTable = Engine_Api::_()->getDbtable('membership', 'user');
    $friendsName = $friendsTable->info('name');

    $select = new Zend_Db_Select($friendsTable->getAdapter());
    $select
      ->from($friendsName, 'user_id')
      ->join($friendsName, "`{$friendsName}`.`user_id`=`{$friendsName}_2`.user_id", null)
      //->join(new Zend_Db_Expr("`$friendsName` AS `friends2`"), "`{$friendsName}`.`user_id`=`friends2`.user_id", null)
      ->where("`{$friendsName}`.resource_id = ?", $viewer->getIdentity())
      ->where("`{$friendsName}_2`.resource_id = ?", $subject->getIdentity())
      ->where("`{$friendsName}`.active = ?", 1)
      ->where("`{$friendsName}_2`.active = ?", 1)
      ;

    // Now get all common friends
    $uids = array();
    foreach( $select->query()->fetchAll() as $data ) {
      $uids[] = $data['user_id'];
    }

    // Do not render if nothing to show
    if( count($uids) <= 0 ) {
      return $this->setNoRender();
    }

    // Get paginator
    $usersTable = Engine_Api::_()->getItemTable('user');
    $select = $usersTable->select()
      ->where('user_id IN(?)', $uids)
      ;

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);

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