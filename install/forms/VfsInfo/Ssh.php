<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Ssh.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_VfsInfo_Ssh extends Engine_Form
{
  public $isSubForm = true;
  
  public function init()
  {
    // init host
    $this->addElement('Text', 'host', array(
      'label' => 'SFTP Host',
      'value' => '127.0.0.1:22',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('host')->getValidator('NotEmpty')
      ->setMessage('Please fill in the SFTP Host field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the SFTP Host field.', 'isEmpty');


    // init user
    $this->addElement('Text', 'username', array(
      'label' => 'SFTP Username',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('username')->getValidator('NotEmpty')
      ->setMessage('Please fill in the SFTP Username field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the SFTP Username field.', 'isEmpty');

    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'SFTP Password',
      'required' => true,
      //'renderPassword' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('password')->getValidator('NotEmpty')
      ->setMessage('Please fill in the SFTP Password field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the SFTP Password field.', 'isEmpty');

    // Init path
    $this->addElement('Text', 'path', array(
      'label' => 'SFTP Path',
      'value' => '/',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('path')->getValidator('NotEmpty')
      ->setMessage('Please fill in the SFTP Path field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the SFTP Path field.', 'isEmpty');
    
    // init search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Search for SocialEngine Path',
      'description' => 'If you don\'t know the exact path to SocialEngine on your server, you can choose to search for it using the "SFTP Path" above as your starting point. Please note that searching may take several minutes. If you do know the exact path, you can uncheck this option.',
      'checked' => true,
      'decorators' => array(
        'ViewHelper',
        //array('HtmlTag', array('tag' => 'div')),
        array('Label', array('placement' => 'APPEND')),
        array('Description', array('placement' => 'PREPEND')),
        'DivDivDivWrapper',
      )
    ));
  }
}