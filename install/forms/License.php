<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: License.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_License extends Engine_Form
{
  public function init()
  {
    // Key
    $this->addElement('Text', 'key', array(
      'label' => 'License Key:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
        new Engine_Validate_Callback(array(get_class($this), 'validateKey'))
      )
    ));
    $this->getElement('key')->getValidator('NotEmpty')
      ->setMessage('Please fill in the license key.', 'notEmptyInvalid')
      ->setMessage('Please fill in the license key.', 'isEmpty');
    $this->getElement('key')->getValidator('Callback')
      ->setMessage('Please enter a valid license key.', 'invalid');

    // Email
    $this->addElement('Text', 'email', array(
      'label' => 'License Email:',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));

    // Statistics
    $this->addElement('Radio', 'statistics', array(
      'label' => 'Allow us to collect information about your server environment?',
      'required' => true,
      'description' => 'With your permission, we would like to collect some '.
        'information about your server to help us improve SocialEngine in the '.
        'future. The exact information we will collect is: PHP version and ' .
        'list of extensions, MySQL version, Web-server type and version, '.
        'SocialEngine version. This information will NOT be shared with any '.
        'third party and will only be used by our development team as we build '.
        'new modules. If you do not wish to send this information, please '.
        'uncheck the box below. We sincerely appreciate your support!',
      'multiOptions' => array(
        '1' => 'Yes, allow information to be reported.',
        '0' => 'No, do not allow information to be reported.',
      ),
      'value' => '1',
    ));

    // Submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue...',
      'type' => 'submit',
      'order' => 10000,
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'form-wrapper submit-wrapper')),
      )
    ));

    $this->addElement('Hidden', 'valid', array(
      'label' => 'Continue...',
      'type' => 'submit',
      'order' => 10001,
    ));
    
    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
  }

  static public function validateKey($value)
  {
    $license = trim($value);
    if( !preg_match("/^[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}[-]{1}[0-9]{4}?$/", $license) ) {
      return false;
    }
    if( substr($license,10,1) * substr($license,11,1) * substr($license,12,1) * substr($license,13,1) != substr($license,15,4) ) {
      return false;
    }
    if( preg_match('/^[0\-]+$/', $license) ) {
      return false;
    }

    return true;
  }
}