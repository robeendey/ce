<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FtpInfo.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_FtpInfo extends Engine_Form
{
  public function init()
  {
    // init host
    $this->addElement('Text', 'host', array(
      'label' => 'FTP Host',
      'value' => '127.0.0.1:21',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('host')->getValidator('NotEmpty')
      ->setMessage('Please fill in the FTP Host field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the FTP Host field.', 'isEmpty');


    // init user
    $this->addElement('Text', 'username', array(
      'label' => 'FTP Username',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('username')->getValidator('NotEmpty')
      ->setMessage('Please fill in the FTP Username field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the FTP Username field.', 'isEmpty');
    
    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'FTP Password',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('password')->getValidator('NotEmpty')
      ->setMessage('Please fill in the FTP Password field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the FTP Password field.', 'isEmpty');

    // Init path
    $this->addElement('Text', 'path', array(
      'label' => 'FTP Path',
      'value' => '/',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('path')->getValidator('NotEmpty')
      ->setMessage('Please fill in the FTP Path field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the FTP Path field.', 'isEmpty');
    
    // init search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Search for SocialEngine Path',
      'description' => 'If you don\'t know the exact path to SocialEngine on your server, you can choose to search for it using the "FTP Path" above as your starting point. Please note that searching may take several minutes. If you do know the exact path, you can uncheck this option.',
      'checked' => true,
      'decorators' => array(
        'ViewHelper',
        //array('HtmlTag', array('tag' => 'div')),
        array('Label', array('placement' => 'APPEND')),
        array('Description', array('placement' => 'PREPEND')),
        'DivDivDivWrapper',
      )
    ));

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper'),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel installation',
      'link' => true,
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'sanity')),
      'decorators' => array('ViewHelper'),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
  }
}