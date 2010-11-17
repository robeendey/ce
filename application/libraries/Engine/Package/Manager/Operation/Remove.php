<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Package
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Remove.php 7533 2010-10-02 09:42:49Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_Package
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_Package_Manager_Operation_Remove
  extends Engine_Package_Manager_Operation_Abstract
{
  protected function _setPackages(Engine_Package_Manifest $targetPackage,
      Engine_Package_Manifest $currentPackage = null)
  {
    $this->_targetPackage = null; //$targetPackage;
    $this->_currentPackage = ( $targetPackage ? $targetPackage : $currentPackage );
  }
  
  public function getSourcePackage()
  {
    return $this->getPackage();
  }
  
  public function getResultantPackage()
  {
    return null;
  }

  public function doInstall()
  {

  }
}