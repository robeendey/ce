<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Api.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_Api extends Engine_Application_Module_Api
{
  public function __call($method, array $args)
  {
    $api = Engine_Api::_()->getApi('core', 'core');
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
}
