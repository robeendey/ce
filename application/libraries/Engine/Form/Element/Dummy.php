<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Dummy.php 7244 2010-09-01 01:49:53Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Form_Element_Dummy extends Zend_Form_Element
{
  protected $_content;

  public function setContent($content)
  {
    $this->_content = $content;
    return $this;
  }

  public function getContent()
  {
    return $this->_content;
  }

  public function render(Zend_View_Interface $view = null)
  {
    $this->removeDecorator('ViewHelper');
    if (null !== $view) {
      $this->setView($view);
    }

    $content = $this->getContent();
    foreach ($this->getDecorators() as $decorator) {
      $decorator->setElement($this);
      $content = $decorator->render($content);
    }
    return $content;
  }
  
  /**
   * Load default decorators
   *
   * @return void
   */
  public function loadDefaultDecorators()
  {
    if( $this->loadDefaultDecoratorsIsDisabled() )
    {
      return;
    }

    $decorators = $this->getDecorators();
    if( empty($decorators) )
    {
      Engine_Form::addDefaultDecorators($this);
    }
  }
}