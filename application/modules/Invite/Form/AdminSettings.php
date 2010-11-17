<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminSettings.php 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Invite
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Invite_Form_AdminSettings extends Engine_Form
{
  public $saved_successfully = FALSE;

  public function init()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    
    // Init form
    $this->setDescription('Modify your invite settings for your members.');

    // Init subject
    $subject = new Zend_Form_Element_Text('subject');
    $subject->setLabel('Subject')
      ->setDescription("This is the subject line of emails sent to invited people.  Use %siteName% to include your site's name.")
      ->setValue($settings->getSetting('invite.subject', ''))
      ->setRequired(true)
      ->setAttrib('size', '90%');

    // Init from fields
    $fromName = new Zend_Form_Element_Text('fromName');
    $fromName->setLabel('From (name)')
      ->setDescription('Whom the invite email is shown as being from (usually your website name)')
      ->setValue($settings->getSetting('invite.fromName', ''))
      ->setRequired(true)
      ->setAttrib('size', '90%');

    $fromEmail = new Zend_Form_Element_Text('fromEmail');
    $fromEmail->setLabel('From (email)')
      ->setDescription('The "Reply-To" address for emails')
      ->setValue($settings->getSetting('invite.fromEmail', 'noreply@'.$_SERVER['HTTP_HOST']))
      ->addValidator('emailAddress', true)
      ->setRequired(true)
      ->setAttrib('size', '90%');

    // Init allow custom invite message
    $allowCustomMessage = new Zend_Form_Element_Checkbox('allowCustomMessage');
    $allowCustomMessage->setLabel('Allow custom invite message?')
      ->setDescription('If disabled, the invite email will use the message below.')
      ->setValue($settings->getSetting('invite.allowCustomMessage', true))
      ->setAttrib('style', 'width: auto;')
      ->setCheckedValue('1');

    // Init default invite message
    $message  = new Zend_Form_Element_Textarea('message');
    $message->setLabel('Default Invite Message')
      ->setDescription('Use %invite_url% to include the invite URL')
      ->setValue($settings->getSetting('invite.message', '%invite_url%'))
      ->setAttrib('rows', 6)
      ->setAttrib('cols', 75);

    //
    // Init submit
    $submit = new Zend_Form_Element_Button('submit');
    $submit->setLabel('Save Settings')
      ->setAttrib('type', 'submit')
      ->removeDecorator('Errors');

    // Add elements
    $this->addElements(array(
      $subject,
      $fromName,
      $fromEmail,
      $allowCustomMessage,
      $message,
      $submit
    ));

  }
  public function saveAdminSettings()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    foreach ($this->getElements() as $element) {
      $key   = $element->getName();
      $value = $element->getValue();
      if ($key != 'submit')
        $settings->setSetting('invite.'.$key, $value);
    }
    $this->saved_successfully = true;

  }
}