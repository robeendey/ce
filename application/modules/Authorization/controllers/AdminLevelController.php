<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminLevelController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Authorization
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Authorization_AdminLevelController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('authorization_admin_main', array(), 'authorization_admin_main_manage');

    $this->view->formFilter = $formFilter = new Authorization_Form_Admin_Level_Filter();
    $page = $this->_getParam('page', 1);

    $table = $this->_helper->api()->getDbtable('levels', 'authorization');
    $select = $table->select();

    if( /*$this->getRequest()->isPost() && */ $formFilter->isValid($this->_getAllParams()) )
    {
      $values = $formFilter->getValues();

      $select = $table->select()
       ->order( !empty($values['orderby']) ? $values['orderby'].' '.$values['orderby_direction'] : 'level_id DESC' );
      
      if ($values['orderby']&& $values['orderby_direction']!='ASC') $this->view->orderby = $values['orderby'];

    }

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
    //$this->view->formDelete = new User_Form_Admin_Manage_Delete();

  }

  public function createAction()
  {
    $this->view->form = $form = new Authorization_Form_Admin_Level_Create();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
    {

      $table = $this->_helper->api()->getDbtable('levels', 'authorization');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
        $values = $form->getValues();
        
        $level = $table->createRow();
        $level->setFromArray($values);
        $level->save();

        //@todo duplicate the settings of given parent value
        // does this go into the authorization_permission table?
        // $values['parent'];
        // select permission for the parent level
        $permissionTable = $this->_helper->api()->getDbtable('permissions', 'authorization');
        $select = $permissionTable->select()->where('level_id = ?', $values['parent']);
        $parent_permissions = $table->fetchAll($select);


        // create permissions
        foreach( $parent_permissions as $parent )
        {
          $permissions = $permissionTable->createRow();
          $permissions->setFromArray($parent->toArray());
          $permissions->level_id = $level->level_id;
          $permissions->save();
        }

        // Commit
        $db->commit();

        // Redirect
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        //$this->_helper->redirector->gotoRoute(array());
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

    }
  }

  public function deleteAction()
  {
    $this->view->form = $form = new Authorization_Form_Admin_Level_Delete();
    $id = $this->_getParam('id', null);

    // check to make sure the level is not default
    $this->view->level = $level = Engine_Api::_()->getItem('authorization_level', $id);

    if($level->flag){
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    if( $id )
    {
      $form->level_id->setValue($id);
    }

    if( $this->getRequest()->isPost() )
    {
      $table = $this->_helper->api()->getDbtable('levels', 'authorization');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try
      {
        // remove all permissions associated with this levle
        $level->removeAllPermissions();

        // reallocate users to default level
        $level->reassignMembers();

        // delete level
        $level->delete();

        // commit
        $db->commit();

        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function editAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('authorization_admin_main', array(), 'authorization_admin_main_level');
    
    // Get level id
    if( null !== ($id = $this->_getParam('id')) ) {
      $this->view->level = $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $this->view->level = $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
      $id = $level->level_id;
    }
   
    $this->view->form = $form = new Authorization_Form_Admin_Level_Edit(array(
      'public' => ( in_array($level->type, array('public')) ),
      'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    
    // Posting form
    if( $this->getRequest()->isPost() )
    {
      if( $form->isValid($this->getRequest()->getPost()) )
      {
        $values = $form->getValues();
        $level->title = $values['title'];
        //$level->description = $values['description'];
        $level->save();

        // update user profile editing permissions
        /*
        $permissionsTable->setAllowed('user', $id, 'edit', $values['edit']);
        // destroy user-edit permission from values array so it doesnt get added to the general perm
        unset($values['edit']);
        */
        
        // set level specific settings for profile, activity and html comments
        $permissionsTable->setAllowed('user', $level->level_id, $values);
     }
    }

    // Initialize data
    else
    {
      $form->populate($level->toArray());
      $form->populate($permissionsTable->getAllowed('user', $id, array_keys($form->getValues())));
      $form->getElement('title')->setValue($level->title);
    }
  }

  public function deleteselectedAction()
  {
    // $this->view->form = $form = new Announcement_Form_Admin_Edit();
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    // $announcement = Engine_Api::_()->getItem('announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);

      foreach ($ids_array as $id){
        $level = Engine_Api::_()->getItem('authorization_level', $id);

        // make sure the ID is not part of the ones that cannot be deleted
        if (!$level->flag){
          // remove all permissions associated with this levle
          $level->removeAllPermissions();

          // reallocate users to default level
          $level->reassignMembers();

          // delete level
          $level->delete();
        }
      }

      //$announcement->delete();
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function setdefaultAction(){
    $id = $this->_getParam('level_id', null);
    $table = $this->_helper->api()->getDbtable('levels', 'authorization');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      // get current default and de-flag the item
      $select = $table->select()
        ->where('flag = ?', 'default')
        ->limit(1);
      $default_level = $table->fetchRow($select);
      $default_level->flag = "";
      $default_level->save();

      // set the current item to default
      $this->view->level = $level = Engine_Api::_()->getItem('authorization_level', $id);
      $level->flag = 'default';
      $level->save();
      $db->commit();

    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
}