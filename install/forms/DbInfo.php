<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: DbInfo.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_DbInfo extends Engine_Form
{
  public function init()
  {
    // init adapters
    /*
    $this->addElement('Select', 'adapter', array(
      'label' => 'Adapter Type:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('adapter')->getValidator('NotEmpty')
      ->setMessage('Please fill in the adapter type.', 'notEmptyInvalid')
      ->setMessage('Please fill in the adapter type.', 'isEmpty');
    
    $adapters = array();
    if( extension_loaded('mysqli') ) {
      $adapters['mysqli'] = 'MySQLi';
    }
    if( extension_loaded('pdo') && extension_loaded('pdo_mysql') ) {
      $adapters['pdo_mysql'] = 'PDO MySQL';
    }
    if( empty($adapters) && extension_loaded('mysql') ) { // Rather not choose this
      $adapters['mysql'] = 'MySQL';
    }
    $this->adapter->setMultiOptions($adapters);
    */
    // init adapter
    $adapterValue = null;
    switch( true ) {
      case ( extension_loaded('mysqli') ):
        $adapterValue = 'mysqli';
        break;
      case ( extension_loaded('pdo') && extension_loaded('pdo_mysql') ):
        $adapterValue = 'pdo_mysql';
        break;
      case ( extension_loaded('mysql') ):
        $adapterValue = 'mysql';
        break;
      default:
        throw new Engine_Exception('No adapters found. This should have been detected by the sanity test.');
        break;
    }
    $this->addElement('Hidden', 'adapter', array(
      'value' => $adapterValue,
    ));


    // init host
    $this->addElement('Text', 'host', array(
      'label' => 'MySQL Host:',
      'value' => 'localhost',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('host')->getValidator('NotEmpty')
      ->setMessage('Please fill in the MySQL Host.', 'notEmptyInvalid')
      ->setMessage('Please fill in the MySQL Host.', 'isEmpty');

    // init username
    $this->addElement('Text', 'username', array(
      'label' => 'MySQL Username:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('username')->getValidator('NotEmpty')
      ->setMessage('Please fill in the MySQL Username.', 'notEmptyInvalid')
      ->setMessage('Please fill in the MySQL Username.', 'isEmpty');

    // init password
    $this->addElement('Password', 'password', array(
      'label' => 'MySQL Password:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->password->renderPassword = true;
    $this->getElement('password')->getValidator('NotEmpty')
      ->setMessage('Please fill in the MySQL Password.', 'notEmptyInvalid')
      ->setMessage('Please fill in the MySQL Password.', 'isEmpty');

    // init dbase
    $this->addElement('Text', 'dbname', array(
      'label' => 'MySQL Database:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('dbname')->getValidator('NotEmpty')
      ->setMessage('Please fill in the MySQL Database.', 'notEmptyInvalid')
      ->setMessage('Please fill in the MySQL Database.', 'isEmpty');

    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue...',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper')),
      )
    ));

    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
  }
}