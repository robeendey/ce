<?php

class Install_Form_Import_Ning extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Ning Import')
      ->setDescription('We will now import your users from the json files
        created by the Ning Archive Tool.')
      ->setAttrib('style', 'width: 650px');
    
    $this->addElement('Text', 'path', array(
      'label' => 'Ning Data Path',
      'description' => 'This is the local folder where the json files and folders of photos are. If you uploaded them to your SocialEngine root, do not change this field.',
      'value' => APPLICATION_PATH,
      'required' => true,
      'allowEmpty' => false,
    ));
    
    $this->addElement('Radio', 'passwordRegeneration', array(
      'label' => 'Password Regeneration',
      'description' => 'Ning does not export your members\' passwords.',
      'multiOptions' => array(
        'random' => 'Email a random password to each member.',
        'none' => 'Do nothing. Members can reset their password using the forgot password link from the login page.',
      ),
      'required' => true,
      'allowEmpty' => false,
      'onchange' => '$("fieldset-mail").setStyle("display", $(this).get("value") != "random" ? "none" : "")',
    ));



    $this->addElement('Text', 'mailFromAddress', array(
      'label' => 'From Address',
      'value' => 'no-reply@' . $_SERVER['HTTP_HOST'],
    ));
    
    $this->addElement('Text', 'mailSubject', array(
      'label' => 'Subject',
      'value' => 'New password for {siteUrl}',
    ));

    $this->addElement('Textarea', 'mailTemplate', array(
      'label' => 'Message Template',
      'allowEmpty' => false,
      'value' => "
Hello {name},

Your password has been regenerated.

Site: {siteUrl}
Email: {email}
Password: {password}

Site Administration
",
      //'style' => 'display: none',
    ));

    $this->addDisplayGroup(array('mailFromAddress', 'mailSubject', 'mailTemplate'), 'mail', array(
      'style' => 'display:none;',
    ));


    //$this->passwordRandomTemplate->getDecorator('HtmlTag2')->setOption('style', 'display:none;');




    $this->addElement('Button', 'execute', array(
      'label' => 'Import',
      'type' => 'submit',
    ));
  }
}
