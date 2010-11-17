<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Invite.php 7341 2010-09-10 03:51:24Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Plugin_Signup_Invite extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'invite';
  protected $_formClass = 'User_Form_Signup_Invite';
  protected $_script = array('signup/form/invite.tpl', 'user');
  protected $_adminFormClass = 'User_Form_Admin_Signup_Invite';
  protected $_adminScript = array('admin-signup/invite.tpl', 'user');
  protected $_skip;

  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    $skip = $request->getParam("skip");
    // do this if the form value for "skip" was not set
    // if it is set, $this->setActive(false); $this->onsubmisvalue and return true.
    if( $skip == "skipForm" ) {
      $this->setActive(false);
      $this->onSubmitIsValid();
      $this->getSession()->skip = true;
      $this->_skip = true;
      return true;
    } else {
      parent::onSubmit($request);
    }
  }

  public function onProcess()
  {
    $data = $this->getSession()->data;
    $form = $this->getForm();
    if( !$this->_skip && !$this->getSession()->skip ) {
      if( $form->isValid($data) ) {
        $user = Engine_Api::_()->user()->getViewer();
        $values = $form->getValues();
        Engine_Api::_()->getDbtable('invites', 'invite')->sendInvites($user, @$values['recipients'], @$values['message']);
      }
    }
  }

  public function onAdminProcess($form)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Invite'));
    $step_row->enable = $form->getValue('enable') && ($settings->getSetting('user.signup.inviteonly') != 1);
    $step_row->save();
  }

}