<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ErrorController.php 7566 2010-10-06 00:18:16Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class ErrorController extends Zend_Controller_Action
{
  public function init()
  {
    // Check if already logged in
    if( !Zend_Registry::get('Zend_Auth')->getIdentity() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
  }
  
  public function errorAction()
  {
    $error = $this->_getParam('error_handler');

    // Log this message
    if( isset($error->exception) &&
        Zend_Registry::isRegistered('Zend_Log') &&
        ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log ) {
      // Only log if in production or the exception is not an instance of Engine_Exception
      $e = $error->exception;
      if( 'production' === APPLICATION_ENV || !($e instanceof Engine_Exception) ) {
        $log->log($e->__toString(), Zend_Log::CRIT);
      }
    }

    // Assign to view
    if( isset($error->exception) ) {
      if( 'development' == APPLICATION_ENV ) {
        $this->view->error = $errStr = $error->exception->__toString();
      } else {
        $this->view->error = $errStr = $error_handler->exception->getMessage();
      }
    }

    // Handle missing pages
    switch( $error->type ) {
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
      case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
        return $this->_forward('notfound');
        break;

      default:
        break;
    }
  }

  public function notfoundAction()
  {
    // 404 error -- controller or action not found
    $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
    $this->view->status = false;
    //$this->view->error = 'The requested resource could not be found.';
  }
}