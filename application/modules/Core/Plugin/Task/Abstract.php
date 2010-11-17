<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Abstract.php 7420 2010-09-20 02:55:35Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Core_Plugin_Task_Abstract
{
  protected $_task;

  protected $_wasIdle = false;

  protected $_log;



  // Main
  
  public function __construct(Zend_Db_Table_Row_Abstract $task)
  {
    if( !($task->getTable() instanceof Core_Model_DbTable_Tasks) ) {
      throw new Core_Model_Exception('Task must belong to the Core_Model_DbTable_Tasks table');
    }
    $this->_task = $task;
  }
  
  public function __call($method, $arguments)
  {
    throw new Core_Model_Exception(sprintf('Unimplemented method %1$s in class %2$s', $method, get_class($this)));
  }
  
  public function getTask()
  {
    return $this->_task;
  }



  // Informational
  
  public function getProgress()
  {
    return null;
  }

  public function getTotal()
  {
    return null;
  }

  public function wasIdle()
  {
    return $this->_wasIdle;
  }



  // Log
  
  /**
   * Get our logger
   *
   * @return Zend_Log
   */
  public function getLog()
  {
    if( null === $this->_log ) {
      $log = new Zend_Log();
      $log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/temporary/log/tasks.log'));
      $this->_log = $log;
    }
    return $this->_log;
  }

  public function setLog(Zend_Log $log)
  {
    $this->_log = $log;
    return $this;
  }



  // Abstract
  
  abstract public function execute();



  // Utility

  protected function _setWasIdle($flag = true)
  {
    $this->_wasIdle = (bool) $flag;
    return $this;
  }
}