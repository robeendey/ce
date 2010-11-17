<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Album_Api_Core extends Core_Api_Abstract
{
  const IMAGE_WIDTH = 720;
  const IMAGE_HEIGHT = 720;
  
  const THUMB_WIDTH = 140;
  const THUMB_HEIGHT = 160;

  protected $_collectible_type = "photo";

  public function createAlbum($params)
  {
    return $this->_createItem("album", $params);
  }


  public function createPhoto($params, $file)
  {
    if( $file instanceof Storage_Model_File )
    {
      $params['file_id'] = $file->getIdentity();
    }
    
    else
    {
      // Get image info and resize
      $name = basename($file['tmp_name']);
      $path = dirname($file['tmp_name']);
      $extension = ltrim(strrchr($file['name'], '.'), '.');
      
      $mainName  = $path.'/m_'.$name . '.' . $extension;
      $thumbName = $path.'/t_'.$name . '.' . $extension;

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::IMAGE_WIDTH, self::IMAGE_HEIGHT)
          ->write($mainName)
          ->destroy();

      $image = Engine_Image::factory();
      $image->open($file['tmp_name'])
          ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
          ->write($thumbName)
          ->destroy();

      // Store photos
      $photo_params = array(
        'parent_id'  => $params['owner_id'], 
        'parent_type'=> $params['owner_type'],
      );

      try {
        $photoFile = Engine_Api::_()->storage()->create($mainName,  $photo_params);
        $thumbFile = Engine_Api::_()->storage()->create($thumbName, $photo_params);
      } catch (Exception $e) {
        if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE)
        {
          echo $e->getMessage();
          exit();
        }
      }

      $photoFile->bridge($thumbFile, 'thumb.normal');

      // Remove temp files
      @unlink($mainName);
      @unlink($thumbName);

      $params['file_id']  = $photoFile->file_id; // This might be wrong
      $params['photo_id'] = $photoFile->file_id;
    }

    $row = Engine_Api::_()->getDbtable('photos', 'album')->createRow();
    $row->setFromArray($params);
    $row->save();
    return $row;

  }

  public function getUserAlbums($user)
  {
    $table = Engine_Api::_()->getItemTable('album');
    return $table->fetchAll($table->select()->where("owner_type = ?", "user")->where("owner_id = ?", $user->user_id));
  }


  public function getAlbumSelect($options = array())
  {
    $table = Engine_Api::_()->getItemTable('album');
    $select = $table->select();
    if( !empty($options['owner']) && $options['owner'] instanceof Core_Model_Item_Abstract )
    {
      $select
        ->where('owner_type = ?', $options['owner']->getType())
        ->where('owner_id = ?', $options['owner']->getIdentity())
        ->order('modified_date DESC')
        ;
    }

    if( !empty($options['search']) && is_numeric($options['search']) )
    {
      $select->where('search = ?', $options['search']);
    }

    return $select;
  }

  public function getAlbumPaginator($options = array())
  {
    return Zend_Paginator::factory($this->getAlbumSelect($options));
  }

  /**
   * Returns a collection of all the categories in the album plugin
   *
   * @return Zend_Db_Table_Select
   */
  public function getCategories()
  {
    $table = Engine_Api::_()->getDbTable('categories', 'album');
    return $table->fetchAll($table->select()->order('category_name ASC'));
  }

  /**
   * Returns a category item
   *
   * @param Int category_id
   * @return Zend_Db_Table_Select
   */
  public function getCategory($category_id)
  {
    return Engine_Api::_()->getDbtable('categories', 'album')->find($category_id)->current();
  }
}
