<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Account.php 7533 2010-10-02 09:42:49Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Admin_Signup_Account extends Engine_Form
{
  public function init()
  {
    $this->setMethod("POST");
    $this->setTitle("Step 1: Create Account");

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $approve = new Engine_Form_Element_Radio('approve');
    $approve->setLabel("Auto-approve Members");
    $approve->setDescription("USER_FORM_ADMIN_SIGNUP_APPROVE_DESCRIPTION");
    $approve->addMultiOptions(
      array(
        1=>'Yes, enable members upon signup.',
        0=>'No, do not enable members upon signup.'
    ));
    $approve->setValue(1);



    $terms = new Engine_Form_Element_Radio('terms');
    $terms->addMultiOptions(
      array(
      1=>'Yes, make members agree to your terms of service on signup.',
      0=>'No, members will not be shown a terms of service checkbox on signup.'
    ));
    $terms->setValue(1);
    $terms->setDescription("USER_FORM_ADMIN_SIGNUP_TERMS_DESCRIPTION");


    $random = new Engine_Form_Element_Radio('random');
    $random->setLabel("Generate Random Passwords?");
    $random->setDescription("USER_FORM_ADMIN_SIGNUP_RANDOM_DESCRIPTION");
    $random->addMultiOptions(
      array(
        1=>'Yes, generate random passwords and email to new members.',
        0=>'No, let members choose their own passwords.'
    ));
    $random->setValue(0);



    $verify_email = new Engine_Form_Element_Radio('verifyemail');
    $verify_email->setLabel("Verify Email Address?");
    $verify_email->setDescription("USER_FORM_ADMIN_SIGNUP_VERIFYEMAIL_DESCRIPTION");
    $verify_email->addMultiOptions(
      array(
        2=>'Yes, verify email addresses.',
        1=>'No, just send members a welcome email',
        0=>'No, do not email new members.'
    ));
    $verify_email->setValue(0);

    $invite_only = new Engine_Form_Element_Radio('inviteonly');
    $invite_only->setDescription("USER_FORM_ADMIN_SIGNUP_INVITEONLY_DESCRIPTION");
    $invite_only->addMultiOptions(
      array(
        2=>'Yes, admins and members must invite new members before they can signup.',
        1=>'Yes, admins must invite new members before they can signup.',
        0=>'No, disable the invite only feature.'
    ));
    $invite_only->setValue(2);



    $check_email = new Engine_Form_Element_Radio('checkemail');
    $check_email->setDescription("USER_FORM_ADMIN_SIGNUP_CHECKEMAIL_DESCRIPTION");
    $check_email->addMultiOptions(
      array(
        1=>"Yes, check that a member's email address was invited.",
        0=>"No, anyone with an invite code can signup."
    ));
    $check_email->setValue(1);
    $check_email->setLabel("Empty");

    /*    $invite_count = new Engine_Form_Element_Text('invitecount');
    $invite_count->setDescription('How many invites do members get when they signup? (If you want to give a particular member extra invites, you can do so via the View Members page. Please enter a number between 0 and 999 below.');
    */ 

    $this->addElements(array($approve, $terms, $random, $verify_email, $invite_only, $check_email));

    $terms->getDecorator('HtmlTag2')->setOption('style', 'border-top:none;clear: right;padding-top:0px;padding-bottom:0px;');

    $invite_only->getDecorator('HtmlTag2')->setOption('style', 'border-top:none;clear:right; padding-top:0px;padding-bottom:0px;');

    $check_email->getDecorator('HtmlTag2')->setOption('style', 'border-top:none; clear:right; float:right;');
    
  //        $invite_count->getDecorator('HtmlTag2')->setOption('style', 'border-top:none; clear:right; float:right;');
    $invite_only->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper signup-invite-wrapper');
    $check_email->getDecorator('HtmlTag2')->setOption('class', 'form-wrapper signup-check-wrapper');
    
    $terms->removeDecorator('label');
    $invite_only->removeDecorator('label');

    $check_email->getDecorator('label')->setOption('tagOptions', array('style'=>'padding-right:0px;visibility:hidden;', 'class'=>'form-label'));

    
    $this->addDisplayGroup(array('terms'), 'term_group');
    $this->addDisplayGroup(array('inviteonly', /*'invitecount',*/ 'checkemail'), 'invite_group');

    $term_group = $this->getDisplayGroup('term_group');
    $invite_group = $this->getDisplayGroup('invite_group');

    $term_group->setLegend("Terms of Service");
    $invite_group->setLegend("Invite Only?");
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
    $this->populate($settings->getSetting('user_signup'));

  }

}