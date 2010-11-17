<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Manage_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('id', 'admin_members_edit')
      ->setTitle('Edit Member')
      ->setDescription('You can change the details of this member\'s account here.');

    // init email
    $this->addElement('Text', 'email', array(
      'label' => 'Email Address'
    ));

    // init username
    $this->addElement('Text', 'username', array(
      'label' => 'Username'
    ));

    // Init level
    $levelMultiOptions = array(); //0 => ' ');
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();

    foreach( $levels as $row )
    {
      $levelMultiOptions[$row->level_id] = $row->getTitle();
    }
    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levelMultiOptions
    ));

    // Init enabled
    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
    ));

    // Init verified
    $this->addElement('Checkbox', 'verified', array(
      'label' => 'Verified?'
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));



    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');



    // Set default action
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }
}