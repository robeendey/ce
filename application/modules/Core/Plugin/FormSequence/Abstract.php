<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Abstract.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Core_Plugin_FormSequence_Abstract implements Core_Plugin_FormSequence_Interface
{
  protected $_name;

  protected $_title;

  protected $_script;

  protected $_adminScript;

  protected $_form;

  protected $_adminForm;

  protected $_formClass;

  protected $_session;

  protected $_registry;
  
  public function getName()
  {
    return $this->_name;
  }

  public function getTitle()
  {
    return $this->_title;
  }

  public function getScript()
  {
    return $this->_script;
  }

  public function getAdminScript()
  {
    return $this->_adminScript;
  }

  public function setScript($script)
  {
    $this->_script = $script;
    return $this;
  }

  public function getForm()
  {
    if( is_null($this->_form) )
    {
      Engine_Loader::loadClass($this->_formClass);
      $class = $this->_formClass;
      $this->_form = new $class();
      $data = $this->getSession()->data;
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

  public function getAdminForm()
  { 
    if( is_null($this->_adminForm) )
    {
      Engine_Loader::loadClass($this->_adminFormClass);
      $class = $this->_adminFormClass;
      $this->_adminForm = new $class();
      $data = $this->getSession()->data;
      if( !empty($data) )
      {
        foreach( $data as $key => $val )
        {
          $el = $this->_adminForm->getElement($key);
          if( $el )
          {
            $el->setValue($val);
          }
        }
      }
    }
    return $this->_adminForm;

  }

  public function setForm(Zend_Form $form)
  {
    $this->_form = $form;
    return $this;
  }
  
  public function setSession(Zend_Session_Namespace $session)
  {
    $this->_session = $session;
    return $this;
  }

  public function getSession()
  {
    if( is_null($this->_session) )
    {
      $this->_session = new Zend_Session_Namespace(get_class($this));
      if( !isset($this->_session->active) )
      {
        $this->_session->active = true;
      }
    }
    return $this->_session;
  }

  public function resetSession()
  {
    $session = $this->getSession();
    $session->unsetAll();
    $session->active = true;
    return $this;
  }

  public function isActive()
  {
    return (bool) $this->getSession()->active;
  }

  public function setActive($flag = false)
  {
    $this->getSession()->active = (bool) $flag;
  }

  public function setRegistry($registry)
  {
    $this->_registry = $registry;
    return $this;
  }
  
  public function onView()
  {
    
  }
  
  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    if( $this->getForm()->isValid($request->getPost()) )
    {
      $this->getSession()->data = $this->getForm()->getValues();
      $this->setActive(false);
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

  public function onSubmitIsValid()
  {

  }

  public function onSubmitNotIsValid()
  {

  }

  public function onProcess(){
  }
}
