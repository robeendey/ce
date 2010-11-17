<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_Api_Core extends Core_Api_Abstract
{
  protected $_transaction = false;

  public function __call($method, array $args)
  {
    $api = Engine_Api::_()->getApi('storage', 'storage');
    if( method_exists($api, $method) )
    {
      //trigger_error("Moved", E_USER_NOTICE);
      $r = new ReflectionMethod($api, $method);
      return $r->invokeArgs($api, $args);
    }
    else
    {
      throw new Exception('method not exist');
    }
  }



  // Transactions

  public function inTransaction()
  {
    return (bool) $this->_transaction;
  }
  
  public function beginTransaction()
  {
    $this->_transaction = true;
    return $this;
  }
  
  public function rollBack()
  {
    $this->_transaction = false;
    foreach( Engine_Api::_()->getApi('storage', 'storage')->getServices() as $service )
    {
      $service->rollBack();
    }
    return $this;
  }

  public function commit()
  {
    $this->_transaction = false;
    foreach( Engine_Api::_()->getApi('storage', 'storage')->getServices() as $service )
    {
      $service->commit();
    }
    return $this;
  }
}