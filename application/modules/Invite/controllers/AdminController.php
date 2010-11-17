<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminController.php 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Invite_AdminController extends Engine_Controller_Action
{
  public function init()
  {
    // Do not show any of this if not logged in && is_admin
    if( !$this->_helper->requireUser()->setNoForward(true)->isValid() ) {
      $session = new Zend_Session_Namespace('Redirect');
      $session->uri = 'admin/invite';
      return $this->_redirect('login');
    }
    parent::init();
  }
  public function indexAction()
  {
    // default to settings page
    $this->_redirect('invite_admin_settings');
  }

  public function statsAction()
  {
    $table  = Engine_Api::_()->getDbtable('invites', 'invite');
    $iName  = $table->info('name');
    $uName  = Engine_Api::_()->getDbtable('users', 'invite')->info('name');
    
    // grab top 10 inviters
    $select = $table->select()
          ->setIntegrityCheck(false)
          ->from($iName, array("$iName.user_id",
                               new Zend_Db_Expr("COUNT(DISTINCT(recipient))     AS invited"),
                               new Zend_Db_Expr('COUNT(DISTINCT(new_user_id))-1 AS recruited'),
                               new Zend_Db_Expr('MAX(new_user_id) AS recruited_max'),
                               "$uName.username"))
          ->joinLeft($uName, "$uName.user_id = $iName.user_id")
          ->group("$uName.user_id")
          ->order(array('invited DESC', 'recruited DESC', 'recruited_max DESC'))
          ->limit(10);

    $this->view->top_inviters = $table->fetchAll($select);
    for ($i=0,$c=count($this->view->top_inviters); $i<$c; $i++) {
      $inviter = $this->view->top_inviters[$i];
      // DISTINCT(new_user_id) could == 0, in which case it works
      // but when inviter invites 1 & they join, then `recruited` is decremented incorrectly
      // the `recruited_max` column is fetched specifically to catch this
      if ($inviter->recruited_max > 0)
        $this->view->top_inviters[$i]->recruited++;
    }

    $select->order(array('recruited DESC', 'invited DESC', 'recruited_max DESC'));
    $this->view->top_recruiters = $table->fetchAll($select);
    for ($i=0,$c=count($this->view->top_recruiters); $i<$c; $i++) {
      $inviter = $this->view->top_recruiters[$i];
      // same as the previous for loop
      if ($inviter->recruited_max > 0)
        $this->view->top_recruiters[$i]->recruited++;
    }
  }

  public function settingsAction()
  {
    $form = new Invite_Form_AdminSettings();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $form->saveAdminSettings();
    }
    $this->view->form = $form;
  }
}