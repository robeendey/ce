<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PersistentAbstract.php 7418 2010-09-20 00:18:02Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Core_Plugin_Task_PersistentAbstract extends Core_Plugin_Task_Abstract
{
  protected $_data;

  protected $_isComplete = false;



  // Main
  
  public function __construct(Zend_Db_Table_Row_Abstract $task)
  {
    parent::__construct($task);
    if( !empty($task->data) ) {
      $this->_data = Zend_Json::decode($task->data);
      if( !is_array($this->_data) ) {
        $this->_data = array();
      }
    }
  }

  public function execute()
  {
    // Execute
    $this->_execute();

    // Save persistent data
    if( is_array($this->_data) && !empty($this->_data) ) {
      $this->_task->data = Zend_Json::encode($this->_data);
    } else {
      $this->_task->data = '';
    }
  }

  abstract protected function _execute();

  

  // Progress
  
  public function getProgress()
  {
    if( is_array($this->_data) ) {
      if( isset($this->_data['progress']) ) {
        return $this->_data['progress'];
      }
    }
    return null;
  }

  public function getTotal()
  {
    if( is_array($this->_data) ) {
      if( isset($this->_data['total']) ) {
        return $this->_data['total'];
      }
    }
    return null;
  }
  
  public function isComplete()
  {
    return (bool) $this->_isComplete;
  }
  
  protected function _setIsComplete($flag)
  {
    $this->_isComplete = (bool) $flag;
    if( $flag ) {
      $this->_data = null;
    }
    return $this;
  }

  

  // Data
  
  public function setPersistentData($data)
  {
    $this->_data = $data;
    return $this;
  }

  public function getPersistentData()
  {
    return $this->_data;
  }

  public function getParam($key, $default = null)
  {
    if( is_array($this->_data) && isset($this->_data[$key]) ) {
      return $this->_data[$key];
    } else {
      return $default;
    }
  }

  public function setParam($key, $value)
  {
    if( !is_array($this->_data) ) {
      $this->_data = array();
    }
    $this->_data[$key] = $value;
    return $this;
  }
}