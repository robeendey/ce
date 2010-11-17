<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: System.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_VfsInfo_System extends Engine_Form
{
  public $isSubForm = true;
  
  public function init()
  {
    // Init path
    $this->addElement('Text', 'path', array(
      'label' => 'Path',
      'value' => APPLICATION_PATH,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('NotEmpty', true),
      ),
    ));
    $this->getElement('path')->getValidator('NotEmpty')
      ->setMessage('Please fill in the Path field.', 'notEmptyInvalid')
      ->setMessage('Please fill in the Path field.', 'isEmpty');

    // init search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Search for SocialEngine Path',
      //'description' => 'If you don\'t know the exact path to SocialEngine on your server, you can choose to search for it using the "SFTP Path" above as your starting point. Please note that searching may take several minutes. If you do know the exact path, you can uncheck this option.',
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