<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Package
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Test.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_Filter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_Package_Manifest_Entity_Test extends Engine_Package_Manifest_Entity_Abstract
{
  protected $_options;
  
  public function __construct($options)
  {
    if( is_array($options) ) {
      $this->_options = $options;
    } else if( $options instanceof Engine_Package_Manifest_Entity_Test ) {
      $this->_options = $options->toArray();
    } else {
      throw new Engine_Package_Manifest_Exception(sprintf('Invalid test data type %s', gettype($options)));
    }
  }

  public function toArray()
  {
    return $this->_options;
  }

  public function fromArray($arr)
  {
    $this->_options = $arr;
    return $this;
  }
}
