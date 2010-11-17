<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Bootstrap.php 7443 2010-09-22 07:25:41Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    
    // Add view helper and action helper paths
    $this->initViewHelperPath();
    $this->initActionHelperPath();

    // Add main user javascript
    //$headScript = new Zend_View_Helper_HeadScript();
    //$headScript->appendFile('application/modules/User/externals/scripts/core.js');

    // Check user online state
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('online', 'user');

    // Check current user
    $table->check($viewer);
  }
}
