<?php

class Core_AdminTasksController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // Make filter form
    $this->view->formFilter = $formFilter = new Core_Form_Admin_Tasks_Filter();

    // Process form
    $values = $this->_getAllParams();
    if( null === $this->_getParam('category') ) {
      $values['category'] = 'system';
    }
    if( !$formFilter->isValid($values) ) {
      $values = array();
    } else {
      $values = $formFilter->getValues();
    }
    $values = array_filter($values);
    $this->view->formFilterValues = $values;

    // Make select
    $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');
    $select = $tasksTable->select()
      ->where('module IN(?)', (array) Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames());

    // Make select - order
    if( empty($values['order']) ) {
      $values['order'] = 'task_id';
    }
    if( empty($values['direction']) ) {
      $values['direction'] = 'ASC';
    }
    $select->order($values['order'] . ' ' . $values['direction']);
    unset($values['order']);
    unset($values['direction']);

    // Make select - where
    if( isset($values['moduleName']) ) {
      $values['module'] = $values['moduleName'];
      unset($values['moduleName']);
    }
    foreach( $values as $key => $value ) {
      $select->where($tasksTable->getAdapter()->quoteIdentifier($key) . ' = ?', $value);
    }

    // Make paginator
    $this->view->tasks = $tasks = Zend_Paginator::factory($select);
    $tasks->setItemCountPerPage(25);
    $tasks->setCurrentPageNumber($this->_getParam('page'));

    // Get task progresses
    $taskProgress = array();
    foreach( $tasks as $task ) {
      try {
        Engine_Loader::loadClass($task->plugin);
        $pluginClass = $task->plugin;
        $plugin = new $pluginClass($task);
        if( $plugin instanceof Core_Plugin_Task_Abstract ) {
          $total = $plugin->getTotal();
          $progress = $plugin->getProgress();
          if( $total || $progress ) {
            $taskProgress[$task->plugin]['progress'] = $plugin->getProgress();
            $taskProgress[$task->plugin]['total'] = $plugin->getTotal();
          }
        }
      } catch( Exception $e ) {
        
      }
    }
    $this->view->taskProgress = $taskProgress;

    // Get task settings
    $this->view->taskSettings = Engine_Api::_()->getApi('settings', 'core')->core_tasks;

    // Get currently executing task info
    $this->view->currentlyExecutingCount = $tasksTable->select()
      ->from($tasksTable->info('name'), new Zend_Db_Expr('COUNT(*)'))
      ->where('executing = ?', 1)
      ->query()
      ->fetchColumn(0);
      ;


    $this->view->navigation = $this->getNavigation();
  }

  public function settingsAction()
  {
    // Get navigation
    $this->view->navigation = $this->getNavigation();

    // Make form
    $this->view->form = $form = new Core_Form_Admin_Tasks_Settings();

    // Get settings
    $current = Engine_Api::_()->getApi('settings', 'core')->core_tasks;

    // Don't allow cron mode on windows
    if( strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ) {
      $form->addError('Cronjob triggering of tasks is not currently supported on Windows.');
      if( $current['mode'] == 'cron' ) {
        if( extension_loaded('curl') ) {
          Engine_Api::_()->getApi('settings', 'core')->core_tasks_mode = $current['mode'] = 'curl';
        } else {
          Engine_Api::_()->getApi('settings', 'core')->core_tasks_mode = $current['mode'] = 'socket';
        }
      }
    }

    // Make sure it's not set to curl if they don't have it
    if( !extension_loaded('curl') && $current['mode'] == 'curl' ) {
      Engine_Api::_()->getApi('settings', 'core')->core_tasks_mode = $current['mode'] = 'socket';
    }

    // Populate form
    $form->populate($current);

    /*
    // Add description
    $form->getElement('mode')
      ->setDescription('test')
      ->getDecorator('Description')
        ->setOption('placement', 'append')
        ;
     * 
     */

    if( $this->getRequest()->isPost() &&
        $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
      if( $values['mode'] == 'cron' ) {
        $values['pid'] = '';
      }
      Engine_Api::_()->getApi('settings', 'core')->core_tasks = $values;
      $current = array_merge($current, $values);
    }
    
    if( $current['mode'] == 'cron' ) {
      $minutes = ceil($current['interval'] / 60);
      $executeUrl = ( _ENGINE_SSL ? 'https://' : 'http://' )
        . $_SERVER['HTTP_HOST']
        . $this->view->url(array('controller' => 'utility', 'action' => 'tasks'), 'default', true)
        . '?'
        . http_build_query(array('key' => $current['key']))
        ;
      $logFile = APPLICATION_PATH . '/temporary/log/tasks.log';
      $commandTemplate = 'echo Cron Execute Result: $(wget -O - %1$s) >> %2$s 2>&1';
      $command = sprintf($commandTemplate, $executeUrl, $logFile);
      
      $form->getDecorator('Description')->setOption('escape', false);
      $form->addNotice($this->view->translate(array(
        'Please set the following command to run in crontab about every %1$s minute: <br />"%2$s"',
        'Please set the following command to run in crontab about every %1$s minutes: <br />"%2$s"',
        $minutes
      ), $minutes, $command));
    }
  }

  public function runAction()
  {
    if( $this->getRequest()->isPost() ) {
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');

      // Single mode
      if( null !== ($task_id = $this->_getParam('task_id')) && is_numeric($task_id) ) {
        $tasks = array($task_id);
      }

      // Multi mode
      else if( null !== ($tasks = $this->_getParam('selection')) && is_array($tasks) ) {
        $tasks = array_filter($tasks);
      }

      if( is_array($tasks) && !empty($tasks) ) {
        $taskObjects = $tasksTable->find($tasks);
        if( null !== $taskObjects ) {
          foreach( $taskObjects as $taskObject ) {
            $tasksTable->executeTask($taskObject);
          }
        }
      }
    }
    
    if( 'json' === $this->_helper->contextSwitch->getCurrentContext() ) {
      $this->view->status = true;
    } else {
      if( null !== ($return = $this->_getParam('return')) ) {
        return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
      } else {
        return $this->_helper->redirector->gotoRoute(array('controller' => 'tasks'), 'admin_default', true);
      }
    }
  }

  public function resetAction()
  {
    if( $this->getRequest()->isPost() ) {
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');

      // Single mode
      if( null !== ($task_id = $this->_getParam('task_id')) && is_numeric($task_id) ) {
        $tasks = array($task_id);
      }

      // Multi mode
      else if( null !== ($tasks = $this->_getParam('selection')) && is_array($tasks) ) {
        $tasks = array_filter($tasks);
      }

      if( is_array($tasks) && !empty($tasks) ) {
        $taskObjects = $tasksTable->find($tasks);
        $tasksTable->resetStats($taskObjects);
      }
    }

    if( 'json' === $this->_helper->contextSwitch->getCurrentContext() ) {
      $this->view->status = true;
    } else {
      if( null !== ($return = $this->_getParam('return')) ) {
        return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
      } else {
        return $this->_helper->redirector->gotoRoute(array('controller' => 'tasks'), 'admin_default', true);
      }
    }
  }

  public function unlockAction()
  {
    if( $this->getRequest()->isPost() ) {
      $tasksTable = Engine_Api::_()->getDbtable('tasks', 'core');

      // Single mode
      if( null !== ($task_id = $this->_getParam('task_id')) && is_numeric($task_id) ) {
        $tasks = array($task_id);
      }

      // Multi mode
      else if( null !== ($tasks = $this->_getParam('selection')) && is_array($tasks) ) {
        $tasks = array_filter($tasks);
      }

      if( empty($tasks) ) {
        $tasks = null;
        $taskObjects = null;
      } else if( is_array($tasks) && !empty($tasks) ) {
        $taskObjects = $tasksTable->find($tasks);
      }

      $tasksTable->resetLocks($taskObjects);
    }

    if( 'json' === $this->_helper->contextSwitch->getCurrentContext() ) {
      $this->view->status = true;
    } else {
      if( null !== ($return = $this->_getParam('return')) ) {
        return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
      } else {
        return $this->_helper->redirector->gotoRoute(array('controller' => 'tasks'), 'admin_default', true);
      }
    }
  }

  public function getNavigation()
  {
    return new Zend_Navigation(array(
      array(
        'label' => 'Task Scheduler',
        'route' => 'admin_default',
        'module' => 'core',
        'controller' => 'tasks',
        'action' => 'index',
        'active' => ( $this->getRequest()->getActionName() == 'index' ),
      ),
      array(
        'label' => 'Task Scheduler Settings',
        'route' => 'admin_default',
        'module' => 'core',
        'controller' => 'tasks',
        'action' => 'settings',
        'active' => ( $this->getRequest()->getActionName() == 'settings' ),
      ),
      array(
        'label' => 'Task Scheduler Log',
        'route' => 'admin_default',
        'module' => 'core',
        'controller' => 'system',
        'action' => 'log',
        'params' => array(
          'file' => 'tasks.log',
        ),
      ),
    ));
  }
}