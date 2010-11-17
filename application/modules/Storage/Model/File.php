<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: File.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_Model_File extends Core_Model_Item_Abstract
{
  // Item stuff

  public function getPhotoUrl($type = null)
  {
    return $this->getStorageService()->map($this);
  }

  public function getHref()
  {
    return $this->getStorageService()->map($this);
  }

  public function getParent()
  {
    return $this->getParent();
  }

  // Storage stuff

  public function getStorageService($type = null)
  {
    $type = ( $type ? $type : $this->storage_type );
    return Engine_Api::_()->getApi('storage', 'storage')->getService($type);
  }

  public function getChildren()
  {
    $table = $this->getTable();
    $select = $table->select()
      ->where('parent_file_id = ?', $this->file_id);
    return $table->fetchAll($select);
  }



  // Simple operations
  
  public function bridge(Storage_Model_File $file, $type, $isChild = false)
  {
    $child  = ( $isChild ? $this : $file );
    $parent = ( $isChild ? $file : $this );
    $child->parent_file_id = $parent->file_id;
    $child->type = $type;
    $child->save();

    return $this;
  }

  public function map()
  {
    return $this->getStorageService()->map($this);
  }

  public function store($spec)
  {
    $service = $this->getStorageService();
    $serviceType = $service->getType();
    
    $meta = $this->getStorageService()->fileInfo($spec);
    $isCreate = empty($this->file_id);
    // Need to initialize file_id
    // @todo this might fubar some things if exception is thrown
    $this->setFromArray($meta);
    $this->storage_type = $serviceType;
    if( $isCreate )
    {
      $this->save();   
    }
    // Store file to service
    $path = $service->store($this, $meta['tmp_name']);

    // We still have to update the path even if we just created it
    $this->storage_path = $path;
    $this->user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->save();
    return $this;
  }
    
  public function write($data, $meta)
  {
    // Note: if this is a new storage, we have to save first to get the id
    $isCreate = empty($this->file_id);

    // Need to initialize file_id
    // @todo this might fubar some things if exception is thrown
    $this->setFromArray($meta);
    $this->storage_type = $this->getStorageService()->getType();
    if( $isCreate )
    {
      $this->save();
    }

    // Write data to service
    $path = $this->getStorageService()->write($this, $meta['tmp_name']);
    
    // We still have to update the path even if we just created it
    $this->storage_path = $partial;
    $this->save();

    return $this;
  }

  public function read()
  {
    return $this->getStorageService()->read($this);
  }

  public function remove()
  {
    $this->getStorageService()->remove($this);
    $this->delete();
  }

  public function temporary()
  {
    return $this->getStorageService()->temporary($this);
  }



  // Complex

  public function move($storage)
  {
    if( is_string($storage) )
    {
      $storage = $this->getStorageService($storage);
    }

    if( !($storage instanceof Storage_Service_Interface) )
    {
      throw new Exception("Storage must be an instance of File_Service_Storage_Interface");
    }

    if( $storage->getType() == $this->getStorageService()->getType() )
    {
      throw new Exception('You may not move a file within a storage type');
    }

    $originalStorage = $this->getStorageService();
    $originalPath = $this->storage_path;

    // Store using temp file
    $tmp_file = $originalStorage->temporary($this);
    $path = $storage->store($this, $tmp_file);

    $this->storage_type = $storage->getType();
    $this->storage_path = $path;
    $this->modified = date('Y-m-d H:i:s');
    $this->save();
    
    // Now remove original and temporary file
    $originalStorage->removeFile($originalPath);
    @unlink($tmp_file);

    return $this;
  }

  public function copy($params = array(), $storage = null)
  {
    $storage = $this->getStorageService($storage);

    if( !($storage instanceof Storage_Service_Interface) )
    {
      throw new Exception("Storage must be an instance of File_Service_Storage_Interface");
    }

    // Create new row
    // @todo store this in main model?
    $params = array_merge($this->toArray(), $params);
    $params['storage_type'] = $storage->getType();
    $params['storage_path'] = 'temp';
    unset($params['file_id']);

    $newThis = $this->getTable()->createRow();
    $newThis->setFromArray($params);
    $newThis->save();

    // Read into temp file and store
    $tmp_file = $this->getStorageService()->temporary($this);
    $path = $storage->store($this, $tmp_file);
    
    // Update
    // @todo make sure file is removed if this fails
    $newThis->storage_path = $path;
    $newThis->save();

    // Remove temp file
    @unlink($tmp_file);

    return $newThis;
  }

  protected function _delete()
  {
    if( $this->_disableHooks ) return;
    
    try {
      $this->getStorageService()->remove($this);
    } catch( Exception $e ) {
      
    }
    //$this->remove();
  }
}