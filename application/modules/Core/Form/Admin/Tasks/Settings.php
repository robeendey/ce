<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Settings.php 7370 2010-09-14 03:28:50Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Tasks_Settings extends Engine_Form
{
  public function init()
  {
    // Set form attributes
    $this
      ->setTitle('Task Scheduler Settings')
      //->setDescription('CORE_FORM_ADMIN_SETTINGS_TASKS_DESCRIPTION')
      ;
    
    // Init mode
    $multiOptions = array();

    if( strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' ) {
      $multiOptions['cron'] = 'Cron-job (Requires setup of cronjob in crontab)';
    }

    if( extension_loaded('curl') ) {
      $multiOptions['curl'] = 'cURL';
    } else {
      $multiOptions['socket'] = 'Socket';
    }
    
    $this->addElement('Select', 'mode', array(
      'label' => 'Trigger Method',
      'multiOptions' => $multiOptions,
    ));

    // Init key
    $this->addElement('Text', 'key', array(
      'label' => 'Trigger Access Key',
    ));
    
    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
}