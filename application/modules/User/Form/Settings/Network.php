<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Network.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Settings_Network extends Engine_Form
{
  public function init()
  {    
    $this
      ->setAttrib('id', 'network-form')
      ->setAttrib('method', 'POST')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    $this->addElement('Text', 'title', array(
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Button', 'execute', array(
      'type' => 'submit',
      'label' => 'Join Network',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Hidden', 'leave_id', array(
      'order' => 990,
    ));
    
    $this->addElement('Hidden', 'join_id', array(
      'order' => 991,
    ));

    $this->loadDefaultDecorators();
    $this->removeDecorator('FormContainer');    
  }
}