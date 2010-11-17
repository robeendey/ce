<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Fields.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Plugin_Signup_Fields extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'fields';

  protected $_title = 'Profile Information';

  protected $_formClass = 'User_Form_Signup_Fields';

  protected $_script = array('signup/form/fields.tpl', 'user');

  protected $_adminFormClass = 'User_Form_Admin_Signup_Fields';

  protected $_adminScript = array('admin-signup/fields.tpl', 'user');

  public function getForm()
  {
    if( is_null($this->_form) )
    {
      $formArgs = array();

      // Preload profile type field stuff
      $profileTypeField = $this->getProfileTypeField();
      if( $profileTypeField ) {
        $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
        $profileTypeValue = @$accountSession->data['profile_type'];
        if( $profileTypeValue ) {
          $formArgs = array(
            'topLevelId' => $profileTypeField->field_id,
            'topLevelValue' => $profileTypeValue,
          );
        }
      }

      // Create form
      Engine_Loader::loadClass($this->_formClass);
      $class = $this->_formClass;
      $this->_form = new $class($formArgs);
      $data = $this->getSession()->data;
      if (empty($data)) {
        $fb_session = new Zend_Session_Namespace('User_AuthController');
        $data = $fb_session->data;
      }
      if( !empty($data) )
      {
        foreach( $data as $key => $val )
        {
          $el = $this->_form->getElement($key);
          if( $el )
          {
            $el->setValue($val);
          }
        }
      }
    }

    return $this->_form;
  }

  public function onView()
  {
  }
  
  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    if( $this->getForm()->isValid($request->getPost()) )
    {
      $this->getSession()->data = $this->getForm()->getProcessedValues();
      $this->getSession()->active = false;
      $this->onSubmitIsValid();
      return true;
    }

    // Form was not valid
    else
    {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }
  }
  
  public function onProcess()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    // Get the newly created viewer

    // Preload profile type field stuff
    $profileTypeField = $this->getProfileTypeField();
    if( $profileTypeField ) {
      $accountSession = new Zend_Session_Namespace('User_Plugin_Signup_Account');
      $profileTypeValue = @$accountSession->data['profile_type'];
      if( $profileTypeValue ) {
        $values = Engine_Api::_()->fields()->getFieldsValues($viewer);
        $valueRow = $values->createRow();
        $valueRow->field_id = $profileTypeField->field_id;
        $valueRow->item_id = $viewer->getIdentity();
        $valueRow->value = $profileTypeValue;
        $valueRow->save();
      }
    }
    
    // Save them values
    $form = $this->getForm()->setItem($viewer);
    $form->setProcessedValues($this->getSession()->data);
    $form->saveValues();

    $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($viewer);
    $viewer->setDisplayName($aliasValues);
    $viewer->save();
  }

  public function getProfileTypeField() {
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if( count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type' ) {
      return $topStructure[0]->getChild();
    }
    return null;
  }
}


