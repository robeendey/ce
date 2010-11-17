<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 7533 2010-10-02 09:42:49Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Signup_Photo extends Engine_Form
{
  public function init()
  {
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Photo'));
    $count = $step_row->order + 1;
    $title = $this->getView()->translate('Step %d:  Add Your Photo', $count);
    $this->setTitle($title)->setDisableTranslator(true);

    $this
      ->setAttrib('enctype', 'multipart/form-data');

    $enable = new Engine_Form_Element_Radio('enable');
    $enable->setLabel("User Photo Upload");
    $enable->setDescription("Do you want your users to be able to upload a photo of themselves upon signup?");
    $enable->addMultiOptions(
      array(
        1=>'Yes, give users the option to upload a photo upon signup.',
        0=>'No, do not allow users to upload a photo upon signup.'
    ));
    $enable->setValue($step_row->enable);

    $this->addElements(array($enable));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    )); 
  }
}