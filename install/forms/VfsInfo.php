<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: VfsInfo.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_Form_VfsInfo extends Engine_Form
{
  protected $_adapterType;

  public function setAdapterType($adapterType)
  {
    $this->_adapterType = $adapterType;
    return $this;
  }

  public function init()
  {
    $this
      ->setTitle('Enter FTP Information')
      ->setDescription('Please provide your FTP login information so that the installer can connect to your server, extract the new files, and set permissions automatically. If you would rather not use FTP to set permissions automatically, you can choose "None" as the connection type and PHP will attempt to set the necessary permissions. This method can be slightly less reliable, so we strongly suggest using FTP.')
      ;

    // init adapter
    $adapterMultiOptions = array();
    $adapterMultiOptions[''] = '';
    $adapterMultiOptions['ftp'] = 'FTP' . ( function_exists('ftp_ssl_connect') ? '/FTPS' : '' );
    if( function_exists('ssh2_connect') && function_exists('ssh2_sftp') ) {
      $adapterMultiOptions['ssh'] = 'SSH/SFTP';
    }
    $adapterMultiOptions['system'] = 'None';
    
    $this->addElement('Select', 'adapter', array(
      'label' => 'FTP Connection Type',
      'required' => true,
      'allowEmpty' => false,
      'multiOptions' => $adapterMultiOptions,
      'onchange' => '$(this).getParent("form").submit();',
      'style' => 'margin-bottom: 15px;',
    ));

    $this->addElement('Hidden', 'previousAdapter', array(
      'order' => 10000,
      'value' => $this->_adapterType,
    ));

    $this->addElement('Hidden', 'step', array(
      'order' => 10001,
      'value' => 'adapter',
    ));

    $this->addElement('Hidden', 'return', array(
      'order' => 10002,
    ));

    $subform = null;
    if( $this->_adapterType ) {
      $class = 'Install_Form_VfsInfo_' . ucfirst($this->_adapterType);
      try {
        if( Zend_Loader_Autoloader::autoload($class) ) {
          $subform = new $class();
        }
      } catch( Exception $e ) {
        $subform = null;
      }
    }

    if( $subform ) {
      $this->prepareSubForm($subform);
      $this->addSubForm($subform, 'config'/*$this->_adapterType*/);

      // Submit
      $this->addElement('Button', 'execute', array(
        'label' => 'Continue',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper'),
      ));

      $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
        'decorators' => array('ViewHelper'),
      ));

      $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
    }
    
    // Modify decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('FormErrors')->setSkipLabels(true);
    
    
    /*
    $ftpForm = new Install_Form_VfsInfo_Ftp();
    $this->prepareSubForm($ftpForm);
    $this->addSubForm($ftpForm, 'ftp');

    $sshForm = new Install_Form_VfsInfo_Ssh();
    $this->prepareSubForm($sshForm);
    $this->addSubForm($sshForm, 'ssh');
    
    $systemForm = new Install_Form_VfsInfo_System();
    $this->prepareSubForm($systemForm);
    $this->addSubForm($systemForm, 'system');

    $this->loadDefaultDecorators();
    $this->removeDecorator('FormErrors');
     * 
     */
  }

  public function prepareSubForm($form)
  {
    $form->clearDecorators()
      //->addDecorator('FormErrors')
      ->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
      ->addDecorator('FormWrapper', array('tag' => 'div', 'class' => 'install_form_vfsinfo', 'id' => strtolower(get_class($form))))
      ;
    $form->setElementsBelongTo('config');
  }

  /*
  public function render(Zend_View_Interface $view = null)
  {
    $type = $this->adapter->getValue();
    foreach( $this->getSubForms() as $subform ) {
      if( substr(strtolower(get_class($subform)), -strlen($type)) != $type ) {
        $subform->getDecorator('FormWrapper')->setOption('style', 'display:none;');
      }
    }

    return parent::render($view);
  }
   * 
   */
}