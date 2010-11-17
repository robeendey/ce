<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSignupController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_AdminSignupController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {    
    $signup_id = $this->getRequest()->getParam('signup_id');
    if (empty($signup_id)) 
    {
      $signup_id = 1;
    }
    $table = $this->_helper->api()->getDbtable('signup', 'user');
    $select = $table->select()
      ->order('order ASC');
    
    $this->view->steps = $table->fetchAll($select);
    $this->view->current_step = $current_step = $table->fetchRow($table->select()->where('signup_id = ?', $signup_id));
    $plugin = new $current_step->class;
    $this->view->script = $plugin->getAdminScript();
    $this->view->form = $form = $plugin->getAdminForm();
    if (!$this->getRequest()->isPost()) 
    {
      return;
    }
    if ($form->isValid($this->getRequest()->getPost())) 
    { 
      $plugin->onAdminProcess($form);
    }
  }

  public function enableAction()
  {

  }

  public function disableAction()
  {

  }

  public function orderAction()
  {
    $table = $this->_helper->api()->getDbtable('signup', 'user');

    if (!$this->getRequest()->isPost())
    {
      return;
    }

    $params = $this->getRequest()->getParams();
    $steps = $table->fetchAll($table->select());

    foreach ($steps as $step)
    {
      $step->order = $this->getRequest()->getParam('step_' . $step->signup_id);
      $step->save();
    }
    return;
  }



  // Steps
  
  public function accountAction()
  {
    $this->view->form = $form = new User_Form_Admin_Signup_Account();
  }

  public function fieldsAction()
  {

  }
}