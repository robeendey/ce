<?php

class Install_Form_Import_Version3 extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('SocialEngine 3 Import')
      ->setDescription('We will now import your users from SocialEngine 3.')
      ->setAttrib('style', 'width: 650px');

    $this->setDescription($this->getDescription() . "
<br />
<a href='javascript:void(0);' onclick='$(\"fieldset-advanced\").setStyle(\"display\", ($(\"fieldset-advanced\").getStyle(\"display\") == \"none\" ? \"\" : \"none\"));'>
  Show Advanced Options
</a>
");
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Text', 'path', array(
      'label' => 'SocialEngine 3 Path',
      'description' => 'This is the local folder where SocialEngine 3 is
        currently installed. It must be properly installed in order to import
        correctly.',
      'value' => realpath($_SERVER['DOCUMENT_ROOT']),
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Text', 'email', array(
      'label' => 'Email Address',
      'description' => 'Progress will be emailed to this address.',
    ));

    $this->addElement('MultiCheckbox', 'emailOptions', array(
      'label' => 'Email Options',
      'multiOptions' => array(
        'start' => 'On Start (to test email works)',
        'step' => 'Each time a step completes',
        'timeout' => 'Every selected number of minutes',
        'warning' => 'When a warning occurs',
        'error' => 'When a recoverable error occurs',
        'fatal' => 'When a fatal error occurs',
        'complete' => 'On Completion',
      ),
      'value' => array(
        'start',
        'fatal',
        'complete',
      ),
    ));

    $this->addElement('Text', 'emailTimeout', array(
      'label' => 'Duration for "Every selected number of minutes" in "Email Options"',
      'value' => 10,
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'Int',
        array('GreaterThan', false, array(0)),
      ),
    ));

    $this->addElement('Radio', 'mode', array(
      'label' => 'Execution Mode',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        'split' => 'Separate requests for each type of data',
        'all' => 'All-at-once',
      ),
      'value' => 'split',
    ));

    $this->addElement('Radio', 'resizePhotos', array(
      'label' => 'Resize Photos?',
      'description' => 'Note: This will make the import process take much longer.',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 1,
    ));

    $this->addElement('Radio', 'skipClearCache', array(
      'label' => 'Skip Clearing the Cache?',
      'required' => true,
      'allowEmpty' => false,
      'description' => 'Note: This may break stuff.',
      'multiOptions' => array(
        1 => 'Yes',
        0 => 'No',
      ),
      'value' => 0,
    ));

    $this->addElement('Text', 'batchCount', array(
      'label' => 'Rows per request',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'Int',
        array('GreaterThan', false, array(0)),
      ),
      'value' => 500,
    ));

    $this->addElement('Text', 'selectCount', array(
      'label' => 'Rows per select',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'Int',
        array('GreaterThan', false, array(0)),
      ),
      'value' => 100,
    ));

    $this->addElement('Text', 'maxAllowedTime', array(
      'label' => 'Max time per request',
      'description' => 'Step will return early if it detects it is going to go over this amount of time (in seconds).',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'Int',
        array('GreaterThan', false, array(0)),
      ),
      'value' => 240,
    ));
    
    $this->addElement('Multiselect', 'disabledSteps', array(
      'label' => 'Disable Steps',
      'description' => 'Select to disable.',
      'style' => 'height: 120px; width: 300px;',
    ));

    $this->addDisplayGroup(array(
      'email',
      'emailOptions',
      'emailTimeout',
      'mode',
      'resizePhotos',
      'skipClearCache',
      'batchCount',
      'selectCount',
      'maxAllowedTime',
      'disabledSteps',
    ), 'advanced', array(
      //'legend' => 'Advanced Options:',
      'style' => 'display:none',
    ));

    $this->addElement('Button', 'execute', array(
      'label' => 'Import',
      'type' => 'submit',
    ));
  }
}