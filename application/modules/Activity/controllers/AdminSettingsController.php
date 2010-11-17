<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettingsController.php 7519 2010-10-01 10:15:28Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Activity_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function generalAction()
  {
    // Make form
    $this->view->form = $form = new Activity_Form_Admin_Settings_General();

    // Populate settings
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $settings->activity;
    unset($values['allowed']);
    $form->populate($values);


    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $values = $form->getValues();
    $allowed = $values['allowed'];
    $list = array_keys($form->getElement('allowed')->getMultiOptions());
    $disallowed = array_diff($list, $allowed);
    unset($values['allowed']);
    
    // Save settings
    $settings->activity = $values;

    // Save action type settings
    if( !empty($disallowed) && is_array($disallowed) ) {
      $actionTypesTable = Engine_Api::_()->getDbTable('actionTypes', 'activity');
      $actionTypesTable->update(array(
        'enabled' => 0,
      ), array(
        'type IN(?)' => (array) $disallowed,
      ));
    }
    if( !empty($allowed) && is_array($allowed) ) {
      $actionTypesTable = Engine_Api::_()->getDbTable('actionTypes', 'activity');
      $actionTypesTable->update(array(
        'enabled' => 1,
      ), array(
        'type IN(?)' => (array) $allowed,
      ));
    }
  }

  public function typesAction()
  {
    $selectedType = $this->_getParam('type');

    // Make form
    $this->view->form = $form = new Activity_Form_Admin_Settings_ActionType();

    // Populate settings
    $actionTypesTable = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $actionTypes = $actionTypesTable->fetchAll();
    $multiOptions = array();
    foreach( $actionTypes as $actionType ) {
      $multiOptions[$actionType->type] = 'ADMIN_ACTIVITY_TYPE_' . strtoupper($actionType->type);
    }
    $form->type->setMultiOptions($multiOptions);

    if( !$selectedType || !isset($multiOptions[$selectedType]) ) {
      $selectedType = key($multiOptions);
    }
    $selectedTypeObject = null;
    foreach( $actionTypes as $actionType ) {
      if( $actionType->type == $selectedType ) {
        $selectedTypeObject = $actionType;
        $form->populate($actionType->toArray());
        // Process mulitcheckbox
        $displayable = array();
        if( 4 & (int) $actionType->displayable ) {
          $displayable[] = 4;
        }
        if( 2 & (int) $actionType->displayable ) {
          $displayable[] = 2;
        }
        if( 1 & (int) $actionType->displayable ) {
          $displayable[] = 1;
        }
        $form->populate(array(
          'displayable' => $displayable,
        ));
      }
    }


    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $values = $form->getValues();
    $values['displayable'] = array_sum($displayable);

    // Check type
    if( !$selectedTypeObject ||
        !isset($multiOptions[$selectedTypeObject->type]) ||
        $selectedTypeObject->type != $values['type'] ) {
      return $form->addError('Please select a valid type');
    }

    unset($values['type']);

    // Save
    $selectedTypeObject->setFromArray($values);
    $selectedTypeObject->save();

    $form->addNotice('Changes saved.');
  }
}