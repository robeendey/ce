<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Storage.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_Api_Storage extends Core_Api_Abstract
{
  protected $_pluginLoader;

  protected $_services = array();

  protected $_files = array();

  protected $_relationships = array();

  const SPACE_LIMIT_REACHED_CODE = 3999;
  /* Services */

  public function getDefaultService()
  {
    //return 'db';
    return 'local';
  }

  public function getService($type = null)
  {
    if( is_null($type) )
    {
      $type = $this->getDefaultService();
    }

    if( !isset($this->_services[$type]) )
    {
      $class = $this->getPluginLoader()->load($type);
      $this->_services[$type] = new $class();
    }

    return $this->_services[$type];
  }

  public function getServices()
  {
    return $this->_services;
  }

  public function get($id, $relationship = null)
  {
    $key = $id . '_' . ( $relationship ? $relationship : 'default' );

    if( !array_key_exists($key, $this->_files) )
    {
      $file = null;
      if( $relationship ) {
        $table = Engine_Api::_()->getItemTable('storage_file');
        $select = $table->select()
          ->where('parent_file_id = ?', $id)
          ->where('type = ?', $relationship)
          ->limit(1);

        $file = $table->fetchRow($select);
      }

      if( null === $file ) {
        $file = Engine_Api::_()->getItem('storage_file', $id);
      }

      $this->_files[$key] = $file;
    }

    return $this->_files[$key];
    
    // If relationship is set, lookup child instead of parent
    /*
    if( !empty($relationship) )
    {
      //$id = $this->lookup($id, $relationship);
      $table = Engine_Api::_()->getDbtable('files', 'storage');
      $select = $table->select()
        ->where('parent_file_id = ?', $id)
        ->where('type = ?', $relationship)
        ->limit(1);

      $file = $table->fetchRow($select);
      if( null !== $file ) {
        return $this->_files[$id] = $file;
      }
    }

    // Check local cache
    if( !array_key_exists($id, $this->_files) )
    {
      $table = Engine_Api::_()->getDbtable('files', 'storage');
      $this->_files[$id] = $table->fetchRow($table->select()->where('file_id = ?', $id)->limit(1));
    }

    return $this->_files[$id];
     * 
     */
  }

  public function lookup($id, $relationship)
  {
    // Cached locally
    if( !isset($this->_relationships[$id][$relationship]) )
    {
      // Lookup in db
      $table = Engine_Api::_()->getDbtable('files', 'storage');
      $select = $table->select()
        ->from($table->info('name'), 'file_id')
        ->where('parent_file_id = ?', $id)
        ->where('type = ?', $relationship)
        ->limit(1);

      $row = $table->fetchRow($select);

      if( null === $row )
      {
        $this->_relationships[$id][$relationship] = false;
      }

      else
      {
        $this->_relationships[$id][$relationship] = $row->file_id;
      }
    }

    if( empty($this->_relationships[$id][$relationship]) )
    {
      return $id;
    }
    
    return $this->_relationships[$id][$relationship];
  }

  public function create($file, $params)
  {
    $params = array_merge(array(
      'storage_type' => $this->getDefaultService()
    ), $params);
    $space_limit = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_quota', 0);
    
    $table = Engine_Api::_()->getDbTable('files', 'storage');
    $table_name = $table->info('name');

    // fetch user
    if( !empty($params['user_id']) &&
        null != ($user = Engine_Api::_()->getItem('user', $params['user_id'])) ) {
      $user_id = $user->getIdentity();
      $level_id = $user->level_id;
    } else if( null != ($user = Engine_Api::_()->user()->getViewer()) ) {
      $user_id = $user->getIdentity();
      $level_id = $user->level_id;
    } else {
      $user_id = null;
      $level_id = null;
    }

    // member level quota
    if( null !== $user_id && null !== $level_id ) {
      $space_limit = (int) Engine_Api::_()->authorization()->getPermission($level_id, 'user', 'quota');
      $space_used = (int) $table->select()
        ->from($table_name, new Zend_Db_Expr('SUM(size) AS space_used'))
        ->where("user_id = ?", (int) $user_id)
        ->query()
        ->fetchColumn(0);
      $space_required = (is_array($file) && isset($file['tmp_name'])
        ? filesize($file['tmp_name']) : filesize($file));

      if( $space_limit > 0 && $space_limit < ($space_used + $space_required) ) {
        throw new Engine_Exception("File creation failed. You may be over your " .
          "upload limit. Try uploading a smaller file, or delete some files to " .
          "free up space. ", self::SPACE_LIMIT_REACHED_CODE);
      }
    }

    $row = Engine_Api::_()->getDbtable('files', 'storage')->createRow();
    $row->setFromArray($params);
    $row->store($file);
    
    return $row;
  }


  public function getStorageLimits()
  {
    return array(
     '1048576' => '1 MB',
     '5242880' => '5 MB',
     '26214400' => '25 MB',
     '52428800' => '50 MB',
     '104857600' => '100 MB',
     '524288000' => '500 MB',
     '1073741824' => '1 GB',
     '2147483648' => '2 GB',
     '5368709120' => '5 GB',
     '10737418240' => '10 GB',
     0 => 'Unlimited'
   );
  }



  public function getPluginLoader()
  {
    if( null === $this->_pluginLoader )
    {
      $path = Engine_Api::_()->getModuleBootstrap('storage')->getModulePath().'/Service/';
      $this->_pluginLoader = new Zend_Loader_PluginLoader(array(
        'Storage_Service' => $path
      ));
    }

    return $this->_pluginLoader;
  }
}