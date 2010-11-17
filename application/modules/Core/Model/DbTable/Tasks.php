<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Tasks.php 7566 2010-10-06 00:18:16Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_Tasks extends Engine_Db_Table
{
  protected $_interval;
  
  protected $_key;

  protected $_mode;

  protected $_pid;

  protected $_timeout;

  protected $_maxJobs;

  protected $_maxTime;


  protected $_externalKey;

  protected $_externalPid;

  protected $_isExecuting = false;

  protected $_isShutdownRegistered = false;

  protected $_runCount = 0;


  protected $_executingTask;

  protected $_log;

  protected $_pendingTasks;

  protected $_tasks;

  

  // Config

  public function getInterval()
  {
    if( null === $this->_interval ) {
      $this->_interval = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.interval', 60);
    }
    return $this->_interval;
  }

  public function getKey()
  {
    if( null === $this->_key ) {
      $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.key');
      // Generate a new key
      if( !$key ) {
        $key = sprintf('%08x', (integer) sprintf('%u', crc32(time() . mt_rand(0, time()))));
        Engine_Api::_()->getApi('settings', 'core')->setSetting('core.tasks.key', $key);
      }
      $this->_key = $key;
    }
    return $key;
  }

  public function getMode()
  {
    if( null === $this->_mode ) {
      $this->_mode = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.mode', 'curl');
    }
    return $this->_mode;
  }
  
  public function getPid()
  {
    if( null === $this->_pid ) {
      $this->_pid = (integer) sprintf('%u', crc32(time() . mt_rand(0, time())));
    }
    return $this->_pid;
  }

  public function getTimeout()
  {
    if( null === $this->_timeout ) {
      $this->_timeout = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.timeout', 900);
    }
    return $this->_timeout;
  }

  public function getMaxJobs()
  {
    if( null === $this->_maxJobs ) {
      $this->_maxJobs = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.maxjobs', 3);
    }
    return $this->_maxJobs;
  }

  public function getMaxTime()
  {
    if( null === $this->_maxTime ) {
      $this->_maxTime = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.maxtime', 120);
    }
    return $this->_maxTime;
  }

  public function getTriggerType()
  {
    return ( $this->getMode() == 'fork' ? 'pre' : 'post' );
  }



  // Request

  public function getExternalKey()
  {
    if( null === $this->_externalKey ) {
      if( isset($_GET['key']) ) {
        $key = $_GET['key'];
      } else if( isset($_POST['key']) ) {
        $key = $_POST['key'];
      } else if( isset($_COOKIE['key']) ) {
        $key = $_COOKIE['key'];
      } else {
        $key = false;
      }
      $this->_externalKey = $key;
    }
    return $this->_externalKey;
  }

  public function setExternalKey($key)
  {
    $this->_externalKey = $key;
    return $this;
  }

  public function getExternalPid()
  {
    if( null === $this->_externalPid ) {
      if( isset($_GET['pid']) ) {
        $pid = $_GET['pid'];
      } else if( isset($_POST['pid']) ) {
        $pid = $_POST['pid'];
      } else if( isset($_COOKIE['pid']) ) {
        $pid = $_COOKIE['pid'];
      } else {
        $pid = false;
      }
      $this->_externalPid = $pid;
    }
    return $this->_externalPid;
  }

  public function setExternalPid($pid)
  {
    $this->_externalPid = $pid;
    return $this;
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


  
  // Triggering

  public function shouldTrigger()
  {
    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Trigger Check: ' . $this->getPid(), Zend_Log::NOTICE);
    }

    // Get base interval
    $table = Engine_Api::_()->getDbtable('settings', 'core');
    $row = $table->fetchRow(array(
      'name = ?' => 'core.tasks.last',
    ));
    if( null === $row ) {
      $table->insert(array(
        'name' => 'core.tasks.last',
        'value' => rand(1, 1000),
      ));
      // Cancel if we're initializing
      return false;
    } else {
      $lastTrigger = $row->value;
    }

    // Get the pid
    $row = $table->fetchRow(array(
      'name = ?' => 'core.tasks.pid',
    ));
    $lastPid = ( !$row || empty($row->value) ? false : $row->value );

    // If we are still executing, make sure delta is larger than the ther interval or timeout
    if( $lastPid && time() < $lastTrigger + max($this->getInterval(), $this->getTimeout()) ) {
      return false;
    }
    // Otherwise, if empty, make sure delta is larger than the min of interval and timeout
    else if( !$lastPid && time() < $lastTrigger + min($this->getInterval(), $this->getTimeout()) ) {
      return false;
    }

    // Update last execution
    $affected = $table->update(array(
      'value' => time(),
    ), array(
      'name = ?' => 'core.tasks.last',
      'value = ?' => $lastTrigger,
    ));

    if( $affected !== 1 ) {
      return false;
    }

    // Update pid
    $table->update(array(
      'value' => $this->getPid(),
    ), array(
      'name = ?' => 'core.tasks.pid',
    ));

    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Trigger Pass: ' . $this->getPid(), Zend_Log::NOTICE);
    }
    
    // Okay, let's go!
    return true;
  }

  public function trigger()
  {
    $mode = $this->getMode();
    if( $mode == 'cron' ) {
      return;
    }

    if( !$this->shouldTrigger() ) {
      return $this;
    }
    
    $method = '_trigger' . ucfirst($mode);

    // Unknown mode
    if( !method_exists($this, $method) ) {
      throw new Core_Model_Exception('Unsupported mode: ' . $mode);
    }

    $prev = ignore_user_abort(true);

    $this->$method();
    
    ignore_user_abort($prev);
    
    return $this;
  }

  protected function _triggerCurl()
  {
    global $generalConfig;
    $code = null;
    if( !empty($generalConfig['maintenance']['code']) ) {
      $code = $generalConfig['maintenance']['code'];
    }

    // Setup
    $host = $_SERVER['HTTP_HOST'];
    $addr = '127.0.0.1'; // $_SERVER['SERVER_ADDR']
    $port = ( !empty($_SERVER['SERVER_PORT']) ? (integer) $_SERVER['SERVER_PORT'] : 80 );
    $path = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'utility', 'action' => 'tasks'), 'default', true)
      . '?notrigger=1'
      . '&key=' . $this->getKey()
      . '&pid=' . $this->getPid()
      ;
    $url = 'http://' . $host . $path;
    
    // Set options
    $multi_handle = curl_multi_init();
    $curl_handle = curl_init();

    curl_setopt($curl_handle, CURLOPT_URL, $url);
    curl_setopt($curl_handle, CURLOPT_PORT, $port);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array('Host: ' . $_SERVER['HTTP_HOST']));

    // Try to handle basic htauth
    if( !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) ) {
      curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curl_handle, CURLOPT_USERPWD, $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
    }

    // Try to handle maintenance mode
    if( $code ) {
      curl_setopt($curl_handle, CURLOPT_COOKIE, 'en4_maint_code=' . $code);
    }
    
    curl_multi_add_handle($multi_handle, $curl_handle);
    
    $active = null;
    //execute the handles
    do {
        $mrc = curl_multi_exec($multi_handle, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
    
    /*
    while ($active && $mrc == CURLM_OK) {
        if (curl_multi_select($multi_handle) != -1) {
            do {
                $mrc = curl_multi_exec($multi_handle, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
     * 
     */
  }

  protected function _triggerSocket()
  {
    global $generalConfig;
    $code = null;
    if( !empty($generalConfig['maintenance']['code']) ) {
      $code = $generalConfig['maintenance']['code'];
    }

    // Setup
    $host = $_SERVER['HTTP_HOST'];
    $addr = '127.0.0.1'; // $_SERVER['SERVER_ADDR']
    $port = ( !empty($_SERVER['SERVER_PORT']) ? (integer) $_SERVER['SERVER_PORT'] : 80 );
    $path = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('controller' => 'utility', 'action' => 'tasks'), 'default', true)
      . '?notrigger=1'
      . '&key=' . $this->getKey()
      . '&pid=' . $this->getPid()
      ;
    $url = 'http://' . $host . $path;

    // Connect
    $handle = fsockopen($addr, $port, $errno, $errstr, 0.5);
    stream_set_blocking($handle, 1);
    if( !$handle ) {
      //echo "$errstr ($errno)<br />\n";
      return;
    } else {
      $out = "GET {$path} HTTP/1.1\r\n";
      $out .= "Host: {$host}\r\n";
      if( !empty($code) ) {
        $out .= "Cookie: en4_maint_code={$code}\r\n";
      }
      $out .= "Connection: Close\r\n\r\n";

      fwrite($handle, $out);

      // Can't close or the remote connection will cancel
      //fclose($handle);
    }
  }

  protected function _triggerCron()
  {
    return false;
  }

  protected function _triggerFork()
  {
    if( function_exists('pcntl_fork') ) {
      $pid = pcntl_fork();
      if( $pid == -1 ) {
        //die('could not fork');
      } else if ($pid) {
        // we are the parent
        //pcntl_wait($status); //Protect against Zombie children
      } else {
        // we are the child
        $this->execute();
      }
    }
  }



  // Tasks

  public function getTasks()
  {
    if( null === $this->_tasks ) {
      $select = $this->select()
        ->where('module IN(?)', (array) Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames())
        ;

      if( in_array('priority', $this->info('cols')) ) {
        $select->order('priority DESC');
      }
      
      $this->_tasks = $this->fetchAll($select);
    }
    return $this->_tasks;
  }

  public function getPendingTasks()
  {
    if( null === $this->_pendingTasks ) {
      $this->_pendingTasks = array();
      foreach( $this->getTasks() as $task ) {
        if( $this->shouldTaskExecute($task, false) ) {
          $this->_pendingTasks[] = $task;
        }
      }
    }
    return $this->_pendingTasks;
    
    /*
    if( null === $this->_pendingTasks ) {
      // Let's also re-calculate the minimum interval
      $min = 3600;

      // Check all tasks
      $this->_pendingTasks = array();
      foreach( $this->getTasks() as $task ) {
        if( $task->timeout > 0 ) {
          $min = min($min, $task->timeout);
        }
        if( $this->shouldTaskExecute($task, false) ) {
          $this->_pendingTasks[] = $task;
        }
      }
      
      // Validate and update minimum interval, if necessary
      if( $min < 60 ) {
        $min = 60;
      } else if( $min > 86400 ) {
        $min = 86400;
      }
      if( $min != $this->getInterval() ) {
        $this->_interval = $min;
        Engine_Api::_()->getApi('settings', 'core')->setSetting('core.tasks.interval', $min);
      }
    }

    return $this->_pendingTasks;
     *
     */
  }

  public function hasPendingTasks()
  {
    return ( count($this->getPendingTasks()) > 0 );
  }



  // Execution

  public function shouldExecute()
  {
    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Execution Check: ' . $this->getPid(), Zend_Log::NOTICE);
    }

    // Check passkey
    if( $this->getExternalKey() != $this->getKey() ) {
      return false;
    }

    // Check pid/mode
    $dbPid = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.tasks.pid');
    if( $this->getMode() != 'none' && $this->getMode() != 'cron' && $this->getExternalPid() != $dbPid ) {
      return false;
    }

    // Check for pending tasks
    if( !$this->hasPendingTasks() ) {
      return false;
    }

    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Execution Pass: ' . $this->getPid(), Zend_Log::NOTICE);
    }

    return true;
  }
  
  public function execute()
  {
    $prev = ignore_user_abort(true);
    $mode = $this->getMode();

    // Update last in cron mode for consistency
    if( $mode == 'cron' ) {
      Engine_Api::_()->getDbtable('settings', 'core')->update(array(
        'value' => time(),
      ), array(
        'name = ?' => 'core.tasks.last',
      ));
    }

    // Check if we should execute
    $shouldExecute = $this->shouldExecute();

    if( $shouldExecute ) {
    
      // Register fatal error handler
      if( !$this->_isShutdownRegistered ) {
        register_shutdown_function(array($this, 'handleShutdown'));
        $this->_isShutdownRegistered = true;
      }

      // Signal execution start
      $this->_isExecuting = true;

      // Set time limit?
      set_time_limit(0);

      // Run pending tasks
      foreach( $this->getPendingTasks() as $task ) {
        // Check if they were run in the background while other tasks were executing
        if( $this->shouldTaskExecute($task, true, true) ) {
          $this->_executeTask($task);
        }
      }
    }

    // Clear pid
    $table = Engine_Api::_()->getDbtable('settings', 'core');
    $table->update(array(
      'value' => '',
    ), array(
      'name = ?' => 'core.tasks.pid',
    ));

    // Signal execution end
    $this->_isExecuting = false;

    if( $shouldExecute ) {
      // Log
      if( APPLICATION_ENV == 'development' ) {
        $this->getLog()->log('Execution Complete: ' . $this->getPid(), Zend_Log::NOTICE);
      }
    }

    // Restore
    ignore_user_abort($prev);

    return $this;
  }

  public function executeTask(Engine_Db_Table_Row $task)
  {
    // Don't run disabled tasks
    if( $task->type == 'disabled' || !$task->enabled ) {
      return $this;
    }

    // Don't start tasks that are already running
    if( $task->state == 'active' || $task->state == 'sleeping' || $task->executing ) {
      return $this;
    }

    // Ok, let's start it
    $task->getTable()->update(array(
      'state' => 'ready',
    ), array(
      'task_id = ?' => $task->task_id,
      'state = ?' => $task->state,
      'executing = ?' => $task->executing,
      'executing_id = ?' => $task->executing_id,
    ));
    
    return $this;
  }
  
  protected function _executeTask(Engine_Db_Table_Row $task)
  {
    // Return if we reached our limit
    if( $this->_runCount >= $this->getMaxJobs() ) {
      return $this;
    }

    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Task Execution Check: ' . $task->title, Zend_Log::NOTICE);
    }

    // Update task using where (this will prevent double executions)
    $affected = $task->getTable()->update(array(
      'state' => 'active',
      'executing' => 1,
      'executing_id' => $this->getPid(),
      'started_last' => time(),
      'started_count' => new Zend_Db_Expr('started_count + 1'),
    ), array(
      'task_id = ?' => $task->task_id,
      'state = ?' => $task->state,
      'executing = ?' => 0,
      'executing_id = ?' => 0,
    ));

    // Refresh
    $task->refresh();
    
    // If not affected cancel
    if( $affected !== 1 ) {
      // $task->executing_id != $this->getPid()
      return $this;
    }

    // Log
    if( APPLICATION_ENV == 'development' ) {
      $this->getLog()->log('Task Execution Pass: ' . $task->title, Zend_Log::NOTICE);
    }
    
    // Set executing task
    $this->_executingTask = $task;

    // Invoke plugin
    $status = false;
    $isComplete = true;
    $wasIdle = false;
    
    try
    {
      // Get plugin
      Engine_Loader::loadClass($task->plugin);

      // Backwards compatibility
      if( !is_subclass_of($task->plugin, 'Core_Plugin_Task_Abstract') ) {
        $this->getLog()->log(sprintf('Task plugin %1$s should extend Core_Plugin_Task_Abstract', $task->plugin), Zend_Log::WARN);

        // Execute plugin
        $plugin = Engine_Api::_()->loadClass($task->plugin);
        if( method_exists($plugin, 'execute') ) {
          $plugin->execute();
        } else if( method_exists($plugin, 'executeTask') ) {
          $plugin->executeTask();
        } else {
          throw new Engine_Exception('Task ' . $task->plugin . ' does not have an execute or executeTask method');
        }
      }

      // Normal
      else {

        // Create plugin object
        $pluginClass = $task->plugin;
        $plugin = new $pluginClass($task);
        $plugin->setLog($this->getLog());

        // Execute
        $plugin->execute();

        // Ask semi auto ones if they are done yet
        if( $task->type == 'semi-automatic' &&
            ($plugin instanceof Core_Plugin_Task_PersistentAbstract ||
            method_exists($plugin, 'isComplete')) ) {
          $isComplete = (bool) $plugin->isComplete();
        }

        // Check was idle
        $wasIdle = $plugin->wasIdle();
      }
      
      $status = true;
    } catch( Exception $e ) {
      // Log exception
      $this->getLog()->log($e->__toString(), Zend_Log::ERR);
      $status = false;
    }

    // Update task
    if( !$isComplete ) {
      $task->state = 'sleeping';
    } else {
      $task->state = 'dormant';
    }
    $task->executing = false;
    $task->executing_id = 0;
    $task->completed_count++;
    $task->completed_last = time();
    if( $status ) {
      $task->success_count++;
      $task->success_last = time();
    } else {
      $task->failure_count++;
      $task->failure_last = time();
    }
    $task->save();

    // Update count
    if( !$wasIdle ) {
      $this->_runCount++;
    }
    
    // Remove executing task
    $this->_executingTask = null;

    // Log
    if( APPLICATION_ENV == 'development' ) {
      if( $status ) {
        $this->getLog()->log('Task Execution Complete: ' . $task->title, Zend_Log::NOTICE);
      } else {
        $this->getLog()->log('Task Execution Complete (with errors): ' . $task->title, Zend_Log::NOTICE);
      }
    }

    return $this;
  }



  // Utility
  
  public function handleShutdown()
  {
    if( $this->_isExecuting ) {
      // This means there was a fatal error during execution

      
      $db = $this->getAdapter();

      // Log
      if( APPLICATION_ENV == 'development' ) {
        $message = '';
        if( function_exists('error_get_last') ) {
          $message = error_get_last();
          $message = $message['type'] . ' ' . $message['message'] . ' ' . $message['file'] . ' ' . $message['line'];
        }
        $this->getLog()->log('Execution Error: ' . $this->getPid() . ' - ' . $message, Zend_Log::NOTICE);
      }
      
      // Let's call rollback just in case the fatal error happened inside a transaction
      // This will restore autocommit
      try {
        $db->rollBack();
      } catch( Exception $e ) {}

      // Cleanup executing task
      if( $this->_executingTask instanceof Zend_Db_Table_Row_Abstract ) {
        $task = $this->_executingTask;
        // Cleanup executing task
        if( $task->type == 'semi-automatic' ) {
          $task->state = 'sleeping'; // Should we do this?
        } else {
          $task->state = 'dormant';
        }
        $task->executing = false;
        $task->executing_id = 0;
        $task->failure_count++;
        $task->failure_last = time();
        $task->completed_count++;
        $task->completed_last = time();
        $task->save();
      }

      // Clear pid
      $table = Engine_Api::_()->getDbtable('settings', 'core');
      $table->update(array(
        'value' => '',
      ), array(
        'name = ?' => 'core.tasks.pid',
      ));

      $this->_isExecuting = false;
    }
  }
  
  public function shouldTaskExecute(Engine_Db_Table_Row $task, $refresh = true, $isAutomatic = false)
  {
    // Refresh to check if run in separate thread
    if( $refresh ) {
      $task->refresh();
    }

    // Don't run it if it's disabled
    if( !$task->enabled || $task->type == 'disabled' ) {
      return false;
    }

    // Don't start manual tasks in automatic mode when they're dormant
    if( in_array($task->type, array('manual', 'semi-automatic')) && $task->state == 'dormant' ) {
      return false;
    }
    
    // Don't start executing tasks again unless they have been executing for > 15 minutes
    // We assume that 15 min means they have died and failed to cancel executing status
    if( $task->executing || $task->state == 'active' ) {
      if( time() > $task->started_last + $this->getTimeout() ) {
        // Update the task, assuming it has timed out
        $newState = ( $task->type == 'semi-automatic' ? 'sleeping' : 'dormant' );
        $task->getTable()->update(array(
          'state' => $newState,
          'executing' => false,
          'executing_id' => 0,
        ), array(
          'task_id = ?' => $task->task_id,
          'state = ?' => $task->state,
          'executing = ?' => $task->executing,
          'executing_id = ?' => $task->executing_id,
        ));
      }
      return false;
    }
    
    // Tasks is not ready to be executed again yet
    if( $task->timeout > 0 ) {
      if( time() < $task->started_last + $task->timeout ) {
        return false;
      }
      if( time() < $task->completed_last + $task->timeout ) {
        return false;
      }
    }

    // Task is dormant
    if( $task->state == 'dormant' ) {
      // Automatic task is ready
      if( $task->type == 'automatic' ) {
        $task->getTable()->update(array(
          'state' => 'ready',
        ), array(
          'task_id = ?' => $task->task_id,
          'state = ?' => $task->state,
        ));
      }
      return false;
    }

    // Sanity check
    if( $task->state != 'ready' && $task->state != 'sleeping' ) {
      $this->getLog()->log('Weird task state: ' . $task->state, Zend_Log::WARN);
      return false;
    }

    // Task is ready
    return true;
  }

  public function resetLocks($tasks = null)
  {
    // Update global locks
    Engine_Api::_()->getDbtable('settings', 'core')->update(array(
      'value' => '',
    ), array(
      'name IN(?)' => array('core.tasks.pid', 'core.tasks.last'),
    ));

    // Update task locks
    // Note: this can cause problems, so make them specify which tasks to break the lock for
    if( null !== $tasks ) {
      
      if( !is_array($tasks) && !($tasks instanceof Zend_Db_Table_Rowset_Abstract) ) {
        $tasks = array($tasks);
      }

      $ids = array();
      foreach( $tasks as $task ) {
        if( is_numeric($task) ) {
          $ids[] = $task;
        } else if( $task instanceof Zend_Db_Table_Row_Abstract &&
            $task->getTable() instanceof Core_Model_DbTable_Tasks ) {
          $ids[] = $task->task_id;
        }
      }
      $tasks = $ids;

      if( !empty($ids) ) {
        $where = array();
        $where['task_id IN(?)'] = $tasks;

        $this->update(array(
          'state' => 'dormant',
          'data' => '',
          'executing' => 0,
          'executing_id' => 0,
          //'started_last' => 0,
          //'started_count' => 0,
          //'completed_last' => 0,
          //'completed_count' => 0,
          //'failure_last' => 0,
          //'failure_count' => 0,
          //'success_last' => 0,
          //'success_count' => 0,
        ), $where);
      }
    }
    
    return $this;
  }

  public function resetStats($tasks = null)
  {
    if( null !== $tasks ) {

      if( !is_array($tasks) && !($tasks instanceof Zend_Db_Table_Rowset_Abstract) ) {
        $tasks = array($tasks);
      }

      $ids = array();
      foreach( $tasks as $task ) {
        if( is_numeric($task) ) {
          $ids[] = $task;
        } else if( $task instanceof Zend_Db_Table_Row_Abstract &&
            $task->getTable() instanceof Core_Model_DbTable_Tasks ) {
          $ids[] = $task->task_id;
        }
      }
      $tasks = $ids;
    }

    if( null === $tasks || !empty($tasks) ) {
      $where = array();
      if( null === $tasks ) {
        $where = null;
      } else if( empty($tasks) ) {
        return;
      } else if( is_numeric($tasks) ) {
        $where['task_id = ?'] = $tasks;
      } else if( is_array($tasks) ) {
        $where['task_id IN(?)'] = $tasks;
      } else {
        return;
      }

      $this->update(array(
        //'state' => 'dormant',
        //'data' => '',
        //'executing' => 0,
        //'executing_id' => 0,
        'started_last' => 0,
        'started_count' => 0,
        'completed_last' => 0,
        'completed_count' => 0,
        'failure_last' => 0,
        'failure_count' => 0,
        'success_last' => 0,
        'success_count' => 0,
      ), $where);
    }
    
    return $this;
  }
}