<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Translate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Translate.php 7244 2010-09-01 01:49:53Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_Translate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Translate extends Zend_Translate
{
  protected $_adapter;
  
  public function __construct($options)
  {
    $this->_adapter = new Engine_Translate_Adapter_Null($options);
  }

  public function getAdapter()
  {
    return $this->_adapter;
  }
  
  public function __call($method, array $options)
  {
      if (method_exists($this->_adapter, $method)) {
          return call_user_func_array(array($this->_adapter, $method), $options);
      }
      require_once 'Zend/Translate/Exception.php';
      throw new Zend_Translate_Exception("Unknown method '" . $method . "' called!");
  }
}