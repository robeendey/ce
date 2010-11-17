<?php

class Install_Form_Sdk_Create extends Engine_Form
{
  public function init()
  {
    $this->setDescription('Creates a skeleton package, with the proper directory structure and manifest files');
    
    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => array(
        '' => '',
        'modules' => 'Module',
        'themes' => 'Theme',
        'widgets' => 'Widget',
        'languages' => 'Language',
        'libraries' => 'Library',
        'externals' => 'External',
        //'plugins' => 'Plugin',
      ),
      'description' => '
        View <a href="javascript:void(0);" onclick="$(\'type_details\').toggle()">Type Descriptions</a>.
        <div id="type_details" style="display:none;">
        <em>Modules</em> are used for full module features (such as Albums or Chat).<br>
        <em>Themes</em> are CSS styles and image-sets for changing the site
          aesthetics. You would use packages instead of regular theme exports
          for versioning your theme.<br>
        <em>Widgets</em> are more-abbreviated display items that do not need full
          Module features or complexity.<br>
        <em>Libraries</em> are sets of classes that will be utilized by other
          widgets or modules.  This is usually reserved for custom libraries
          that you did not write but want to utilize.<br>
        <em>Externals</em> are things like CSS, Javascript, or Image sets that do
          not require any server-side processing.<br>
        <!--<em>Plugins</em> ... I have no idea how those differ from widgets.-->
        </div>',
    ));
    $this->type->getDecorator('Description')->setOption('escape', false)
      ->setOption('placement', 'APPEND');
    
    $this->addElement('Text', 'name', array(
      'label' => 'Name',
      'description' => 'Lowercase, alphanumeric, and hyphens ("-") only',
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        array('Regex', true, array('/^[a-z][a-z0-9-]+$/')),
      ),
    ));
    $this->name->getDecorator('Description')->setOption('escape', false)
      ->setOption('placement', 'APPEND');

    $this->addElement('Text', 'title', array(
      'label' => 'Title',
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
    ));

    $this->addElement('Text', 'author', array(
      'label' => 'Author',
    ));

    $this->addElement('Text', 'version', array(
      'label' => 'Version',
      'description' => 'See <a target="_blank" href="http://www.php.net/version_compare">version_compare()</a> for compatible formats.',
      'value' => '4.0.0',
      'required' => true,
      'allowEmpty' => false,
    ));
    $this->version->getDecorator('Description')->setOption('escape', false)
      ->setOption('placement', 'APPEND');
    
    $this->addElement('Text', 'date', array(
      'label' => 'Build Date',
      'description' => 'See <a target="_blank" href="http://www.php.net/strtotime">strtotime()</a> for compatible formats',
      'value' => date('r'),
    ));
    $this->date->getDecorator('Description')->setOption('escape', false)
      ->setOption('placement', 'APPEND');

    $this->addElement('Button', 'execute', array(
      'label' => 'Create Package',
      'decorators' => array('ViewHelper'),
      'type' => 'submit',
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'prependText' => ' or ',
      'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
      'link' => true,
      'decorators' => array('ViewHelper'),
    ));

    $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
  }
}