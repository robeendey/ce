<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Ads_Create extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this->setTitle('Create Advertising Campaign');
    $this->setDescription('Follow this guide to design and launch a new advertising campaign.');

    // Element: name
    $this->addElement('Text', 'name', array(
      'label' => 'Campaign Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    // Element: start_time
    $this->addElement('CalendarDateTime', 'start_time', array(
      'label' => 'Start Date',
      'value' => date('M d Y'),
    ));

    // Element: end_settings
    $this->addElement('Radio', 'end_settings', array(
      'id'=>'end_settings',
      'label' => 'End Date',
      'onchange' => "updateTextFields(this)",
      'multiOptions' => array(
        "0" =>  "Don't end this campaign on a specific date.",
        "1" =>  "End this campaign on a specific date."
      ),
      'value' => 0
    ));

    // Element: end_time
    $this->addElement('CalendarDateTime', 'end_time', array(
      'value' => date('M d Y'),
      'ignoreValid' => true,
    ));

    // Element: limit_view
    $this->addElement('Text', 'limit_view', array(
      'label' => 'Total Views Allowed',
      'description' => 'The campaign will end when this number of views is reached. Enter "0" for unlimited views.',
      'class' => 'short',
      'value' => '0'
    ));
    $this->limit_view->getDecorator('Description')->setOption('placement', 'append');

    // Element: limit_click
    $this->addElement('Text', 'limit_click', array(
      'label' => 'Total Clicks Allowed',
      'description' => 'The campaign will end when this number of clicks is reached. Enter "0" for unlimited clicks.',
      'class' => 'short',
      'value' => '0'
    ));
    $this->limit_click->getDecorator('Description')->setOption('placement', 'append');

    // Element: limit_ctr
    $this->addElement('Text', 'limit_ctr', array(
      'label' => 'Minimum CTR',
      'description' => 'CORE_FORM_ADMIN_ADS_CREATE_LIMITCTR_DESCRIPTION',
        'class' => 'short',
        'value' => '0'
    ));
    $this->limit_ctr->getDecorator('Description')->setOption('placement', 'append');


    // Ad Campaign Audience Stuff
    // prepare levels
    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach ($levels as $level){
      $levels_prepared[$level->getIdentity()]= $level->getTitle();
    }
    reset($levels_prepared);
    $this->addElement('Multiselect', 'ad_levels', array(
      'label' => 'Member Levels',
      'description' => 'CORE_FORM_ADMINS_ADS_CREATE_ADLEVELS_DESCRIPTION',
      'multiOptions' => $levels_prepared,
      'value' => key($levels_prepared),
    ));

    // prepare networks
    // $networks = Engine_Api::_()->network()->getNetwork(NULL, NULL);
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();

    if(count($networks)>0){
      foreach ($networks as $network){
        $networks_prepared[$network->getIdentity()]= $network->getTitle();
      }
      reset($networks_prepared);

      $this->addElement('Multiselect', 'ad_networks', array(
        'label' => 'Networks',
        'description' => 'CORE_FORM_ADMINS_ADS_CREATE_ADNETWORKS_DESCRIPTION',
        'multiOptions' => $networks_prepared,
        'value' => key($networks_prepared)
      ));
    }
    // Public
    $this->addElement('Checkbox', 'public', array(
      'label' => 'Show this advertisement to visitors that are not logged in.',
      'value' => 1
    ));

    $this->addElement('Button', 'submit', array(
            'label' => 'Create Campaign',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'core', 'controller' => 'ads', 'action' => 'index'), 'admin_default', true),
            'decorators' => array(
                    'ViewHelper'
            )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');

  }
}