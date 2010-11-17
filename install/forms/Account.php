<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Account.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_Account extends Engine_Form
{
  public function init()
  {
    // init site title
    $this->addElement('Text', 'site_title', array(
      'label' => 'Community Title',
      'required' => true,
      'description' => 'Provide a brief, descriptive title for your community.',
      'value' => 'My Community',
      'class' => 'long',
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->site_title->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->site_title->getValidator('NotEmpty')
      ->setMessage('Please fill in the Community Title.', 'isEmpty');

    // init email
    $this->addElement('Text', 'email', array(
      'label' => 'Admin Email Address',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'You will sign in with this email address.',
      'validators' => array(
        array('NotEmpty', true),
        array('EmailAddress', true),
      ),
    ));
    $this->email->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->email->getValidator('NotEmpty')
      ->setMessage('Please fill in the Email Address.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Email Address.', 'isEmpty');

    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'Admin Password',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'You will sign in with this password.',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(6, 32)),
      ),
    ));
    $this->password->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->password->renderPassword = true;
    $this->password->getValidator('NotEmpty')
      ->setMessage('Please fill in the Password.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Password.', 'isEmpty');

    // init password again
    $this->addElement('Password', 'password_conf', array(
      'label' => 'Admin Password Again',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Enter the same password again to confirm.',
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->password_conf->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->password_conf->renderPassword = true;
    $this->password_conf->getValidator('NotEmpty')
      ->setMessage('Please fill in the Password Again.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Password Again.', 'isEmpty');

    // init username
    $this->addElement('Text', 'username', array(
      'label' => 'Admin Profile Address',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Choose what the end of your profile URL will look like. For example, if you enter "admin", your profile URL will look something like "www.yoursite.com/profile/admin"',
      'validators' => array(
        array('NotEmpty', true),
        array('Alnum', true),
        array('StringLength', true, array(4, 64)),
        array('Regex', true, array('/^[a-z0-9]/i')),
      ),
    ));
    $this->username->getDecorator('Description')->setOption('placement', 'APPEND');
    $this->username->getValidator('NotEmpty')
      ->setMessage('Please fill in the Profile Address.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Profile Address.', 'isEmpty');
    
    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
    ));

    //$this->addDisplayGroup(array('submit'), 'buttons');

    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
  }
}