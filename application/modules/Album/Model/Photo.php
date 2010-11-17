<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 7418 2010-09-20 00:18:02Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Album_Model_Photo extends Core_Model_Item_Collectible
{
  protected $_searchTriggers = array('title', 'description', 'search');

  protected $_collection_type = "album";

  public function getHref($params = array())
  {
    $params = array_merge(array(
          'route' => 'album_extended',
          'reset' => true,
          'controller' => 'photo',
          'action' => 'view',
          'album_id' => $this->collection_id,
          'photo_id' => $this->getIdentity(),
            ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }

  public function getCollection()
  {
    return Engine_Api::_()->getItem('album', $this->collection_id);
  }

  public function getParent($type = null)
  {
    if( null === $type ) {
      return $this->getCollection();
    } else {
      return $this->getCollection()->getParent($type);
    }
  }

  /**
   * Gets a url to the current photo representing this item. Return null if none
   * set
   *
   * @param string The photo type (null -> main, thumb, icon, etc);
   * @return string The photo url
   */
  public function getPhotoUrl($type = null)
  {
    $photo_id = $this->file_id;
    if( !$photo_id ) {
      return null;
    }

    $file = $this->api()->getApi('storage', 'storage')->get($photo_id, $type);
    if( !$file ) {
      return null;
    }

    return $file->map();
  }

  public function isSearchable()
  {
    $collection = $this->getCollection();
    if( !$collection instanceof Core_Model_Item_Abstract ) {
      return false;
    }
    return $collection->isSearchable();
  }

  public function getAuthorizationItem()
  {
    return $this->getCollection();
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   * */
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   * */
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the tags handler
   *
   * @return Engine_ProxyObject
   * */
  public function tags()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('tags', 'core'));
  }

  public function isOwner($user)
  {
    if( empty($this->collection_id) ) {
      return (($this->owner_id == $user->getIdentity()) && ($this->owner_type == $user->getType()));
    }
    return parent::isOwner($user);
  }

  protected function _postDelete()
  {
    // This is dangerous, what if something throws an exception in postDelete
    // after the files are deleted?
    try {
      $file = $this->api()->getApi('storage', 'storage')->get($this->file_id, null);
      //$file->remove();
      $file = $this->api()->getApi('storage', 'storage')->get($this->file_id, 'thumb.normal');
      //$file->remove();
      //$file = $this->api()->getApi('storage', 'storage')->get($this->file_id, 'croppable');
      //$file->remove();

      $album = $this->getCollection();
      $nextPhoto = $this->getNextCollectible();

      if( ($album instanceof Core_Model_Item_Collection) && ($nextPhoto instanceof Core_Model_Item_Collectible) &&
          (int) $album->photo_id == (int) $this->getIdentity() ) {
        $album->photo_id = $nextPhoto->getIdentity();
        $album->save();
      }
    } catch( Exception $e ) {
      // @todo should we completely silence the errors?
      //throw $e;
    }

    parent::_postDelete();
  }

}
