<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Package
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Theme.php 7533 2010-10-02 09:42:49Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_Filter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_Package_Installer_Theme extends Engine_Package_Installer_Abstract
{
  public function onInstall()
  {
    $db = $this->getDb();
    $package = $this->getOperation()->getPrimaryPackage();

    $newInfo = array(
      'name' => (string) $package->getName(),
      //'version' => $package->getVersion(),
      'title' => (string) $package->getTitle(),
      'description' => (string) $package->getDescription(),
    );

    try {
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_themes')
        ->where('name = ?', $package->getName())
        ->limit(1);

      $oldInfo = $select->query()->fetch();

      if( empty($oldInfo) ) {
        $db->insert('engine4_core_themes', $newInfo);
      } else {
        $db->update('engine4_core_themes', $newInfo, array(
          'name = ?' => $package->getName(),
        ));
      }
    } catch( Exception $e ) {
      $this->_error('Unable to update theme info.');
      return $this;
    }

    $this->_message('Theme info updated.');

    return $this;
  }
}