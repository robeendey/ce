<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Themes.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_DbTable_Themes extends Engine_Db_Table
{

  /**
   * Deletes all temporary files in the Scaffold cache
   *
   * @example self::clearScaffoldCache();
   * @return void
   */
  public static function clearScaffoldCache()
  {
    foreach (glob(APPLICATION_PATH . '/temporary/scaffold/*.css') as $cached_css) {
      @unlink($cached_css);
    }
    try {
      Engine_Package_Utilities::fsRmdirRecursive(APPLICATION_PATH . '/temporary/scaffold/application/');
    } catch (Exception $e){}
  }
}
