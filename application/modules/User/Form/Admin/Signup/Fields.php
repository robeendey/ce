<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Fields.php 7533 2010-10-02 09:42:49Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Signup_Fields extends Engine_Form
{
  public function init()
  {
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Fields'));
    $count = $step_row->order + 1;
    $title = $this->getView()->translate('Step %d: Create Profile', $count);
    $this->setTitle($title)->setDisableTranslator(true);
    
    $description = $this->getView()->translate("USER_FORM_ADMIN_SIGNUP_FIELDS_DESCRIPTION",Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module'=>'user', 'controller'=>'fields'), 'admin_default', true));
    $this->setDescription($description);
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);
  }
}