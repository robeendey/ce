<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Files.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_Model_DbTable_Files extends Engine_Db_Table
{
  //protected $_name = 'files';

  protected $_rowClass = 'Storage_Model_File';

  /*
  public function lookup($id, $relationship)
  {
    // Query
    $select = $this->select()
      ->from($this->info('name'), 'file_id')
      ->where('parent_id = ?', $id)
      ->where('parent_relationship = ?', $relationship)
      ->limit(1);

    $data = $this->fetchRow($select);

    // Not found
    if( !isset($data) || !isset($data->file_id) )
    {
      return null;
    }

    // Found
    return $data->file_id;
  }

  public function link($parent, $child, $relationship)
  {
    $parent = $this->_getFileId($parent);
    $child = $this->_getFileId($child);

    return (bool) $this->update(array(
      'parent_id' => $parent,
      'parent_relationship' => $relationship
    ), $this->getAdapter()->quoteInto('file_id = ?', $child));
  }

  protected function _getFileId($file)
  {
    if( is_numeric($file) )
    {
      $id = $file;
    }

    else if( $file instanceof Storage_Model_DbRow_File )
    {
      $id = $file->file_id;
    }
    
    if( !$id )
    {
      throw new Exception("File must be an id or File_Model_DbRow_File object");
    }

    return $id;
  }
   * 
   */
}