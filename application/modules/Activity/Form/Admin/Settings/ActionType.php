<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: General.php 7376 2010-09-14 05:58:07Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Activity_Form_Admin_Settings_ActionType extends Engine_Form
{
  //protected $_isArray = true;

  public function init()
  {
    $this
      ->setTitle('Activity Feed Item Type Settings')
      ->setDescription('On this page you can change per item type settings. ' .
          'Note that disabling an item prevents it from being created; ' .
          'whereas an item set to not displayable will still be created, ' .
          'but will not be visible.')
      ;
    
    $this->addElement('Select', 'type', array(
      //'ignore' => true,
      'label' => 'Type',
    ));

    $this->addElement('Radio', 'enabled', array(
      'label' => 'Enabled?',
      'description' => 'The other settings on this page will have ' .
        'no effect if this item is disabled.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
//      'decorators' => array(
//        'Label',
//        'ViewHelper',
//      ),
    ));

    $this->addElement('Radio', 'shareable', array(
      'label' => 'Shareable?',
      'description' => 'Can members share this activity feed item type?',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
//      'decorators' => array(
//        'Label',
//        'ViewHelper',
//      ),
    ));

    $this->addElement('MultiCheckbox', 'displayable', array(
      'label' => 'Display',
      'description' => 'Which types of feeds should this item be displayed in?',
      'multiOptions' => array(
        4 => 'Main feed',
        2 => 'Object\'s profile feed',
        1 => 'Subject\'s profile feed',
      ),
//      'decorators' => array(
//        'Label',
//        'ViewHelper',
//      ),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  /*
  public function getLabel()
  {
    return $this->getTitle();
  }

  public function isRequired()
  {
    return null;
  }

  public function getAllowEmpty()
  {
    return null;
  }
  
  public function loadDefaultDecorators()
  {
    if ($this->loadDefaultDecoratorsIsDisabled()) {
      return;
    }

    $decorators = $this->getDecorators();
    if (empty($decorators)) {
      $fqName = $this->getName();
      $this
        ->addDecorator('FormElements')
        ->addDecorator('Description', array('tag' => 'p', 'class' => 'description', 'placement' => 'PREPEND'))
        ->addDecorator('HtmlTag', array('tag' => 'div', 'id'  => $fqName . '-element', 'class' => 'form-element'))
        ->addDecorator('Label', array('tag' => 'div', 'tagOptions' => array('id' => $fqName . '-label', 'class' => 'form-label')))
        ->addDecorator('HtmlTag2', array('tag' => 'div', 'id'  => $fqName . '-wrapper', 'class' => 'form-wrapper'));
        ;
    }
  }
   * 
   */
}