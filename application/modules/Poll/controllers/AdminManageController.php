<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 7518 2010-10-01 09:27:40Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Poll_AdminManageController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('poll_admin_main', array(), 'poll_admin_main_manage');
 
    $select    = Engine_Api::_()->getApi('core', 'poll')->getPollSelect();
    $select->orWhere('1=1'); // display all polls
    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
    $users     = array();
  }

  public function deleteAction()
  {
    // In smoothbox
    $this->_helper->layout->setLayout('admin-simple');
    $id = $this->_getParam('id');
    $this->view->poll_id=$id;
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $poll = Engine_Api::_()->getItem('poll', $id);
        $poll->delete();
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh'=> 10,
          'messages' => array('')
      ));
    }
    // Output
    $this->renderScript('admin-manage/delete.tpl');
  }
  
  public function deleteselectedAction()
  {
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach( $ids_array as $id ){
        $poll = Engine_Api::_()->getItem('poll', $id);
        if( $poll ) $poll->delete();
      }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }

  public function suggestAction()
  {
    $page = $this->_getParam('page');
    $query = $this->_getParam('query');
    
    $pollTable = Engine_Api::_()->getItemTable('poll');
    $pollSelect = $pollTable->select()
      ->where('title LIKE ?', '%' . $query . '%');
    $paginator = Zend_Paginator::factory($pollSelect);
    $paginator->setCurrentPageNumber($page);

    $data = array();
    foreach( $paginator as $poll ) {
      $data[$poll->poll_id] = $poll->getTitle();
    }
    $this->view->status = true;
    $this->view->data = $data;
  }

  public function infoAction()
  {
    $pollIdentity = $this->_getParam('poll_id');
    if( !$pollIdentity ) {
      $this->view->status = false;
      return;
    }

    $poll = Engine_Api::_()->getItem('poll', $pollIdentity);
    if( !$poll ) {
      $this->view->status = false;
      return;
    }

    $this->view->status = true;
    $this->view->identity = $poll->getIdentity();
    $this->view->title = $poll->getTitle();
    $this->view->description = $poll->getDescription();
    $this->view->href = $poll->getHref();
  }
}