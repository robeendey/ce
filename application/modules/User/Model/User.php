<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: User.php 7425 2010-09-20 03:50:23Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Model_User extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = array('search', 'displayname', 'username');
  /**
   * Gets the title of the user (their username)
   *
   * @return string
   */
  public function getTitle()
  {
    // This will cause various problems
    //$viewer = Engine_Api::_()->user()->getViewer();
    //if( $viewer->getIdentity() && $viewer->getIdentity() == $this->getIdentity() )
    //{
    //  $translate = Zend_Registry::get('Zend_Translate');
    //  return $translate->translate('You');
    //}
    if( isset($this->displayname) && '' !== trim($this->displayname) ) {
      return $this->displayname;
    } else if( isset($this->username) && '' !== trim($this->username) ) {
      return $this->username;
    } else {
      return "<i>" . Zend_Registry::get('Zend_Translate')->_("Deleted Member") . "</i>";
    }
  }

  /**
   * Gets an absolute URL to the page to view this item
   *
   * @return string
   */
  public function getHref($params = array())
  {
    $profileAddress = null;
    if( isset($this->username) && '' != trim($this->username) ) {
      $profileAddress = $this->username;
    } else if( isset($this->user_id) && $this->user_id > 0 ) {
      $profileAddress = $this->user_id;
    } else {
      return 'javascript:void(0);';
    }
    
    $params = array_merge(array(
      'route' => 'user_profile',
      'reset' => true,
      'id' => $profileAddress,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }


  public function setDisplayName($displayName)
  {
    if( is_string($displayName) )
    {
      $this->displayname = $displayName;
    }

    else if( is_array($displayName) )
    {
      // Has both names
      if( !empty($displayName['first_name']) && !empty($displayName['last_name']) )
      {
        $displayName = $displayName['first_name'].' '.$displayName['last_name'];
      }
      // Has full name
      else if( !empty($displayName['full_name']) )
      {
        $displayName = $displayName['full_name'];
      }
      // Has only first
      else if( !empty($displayName['first_name']) )
      {
        $displayName = $displayName['first_name'];
      }
      // Has only last
      else if( !empty($displayName['last_name']) )
      {
        $displayName = $displayName['last_name'];
      }
      // Has neither (use username)
      else
      {
        $displayName = $this->username;
      }
      
      $this->displayname = $displayName;
    }
  }

  public function setPhoto($photo)
  {
    if( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->file_id)->temporary();
    } else if( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }

    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
      'parent_type' => $this->getType(),
      'parent_id' => $this->getIdentity()
    );

    // Save
    $storage = Engine_Api::_()->storage();

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(140, 160)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();

    // Store
    $iMain = $storage->create($path.'/m_'.$name, $params);
    $iProfile = $storage->create($path.'/p_'.$name, $params);
    $iIconNormal = $storage->create($path.'/in_'.$name, $params);
    $iSquare = $storage->create($path.'/is_'.$name, $params);

    $iMain->bridge($iProfile, 'thumb.profile');
    $iMain->bridge($iIconNormal, 'thumb.normal');
    $iMain->bridge($iSquare, 'thumb.icon');

    // Remove temp files
    @unlink($path.'/p_'.$name);
    @unlink($path.'/m_'.$name);
    @unlink($path.'/in_'.$name);
    @unlink($path.'/is_'.$name);

    // Update row
    $this->modified_date = date('Y-m-d H:i:s');
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    return $this;
  }

  public function isEnabled()
  {
    return ( $this->enabled && $this->verified );
  }


  // Internal hooks

  protected function _insert()
  {
    parent::_insert();
    
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // Set defaults, process etc
    $this->salt = (string) rand(1000000, 9999999);
    $this->password = md5( $settings->getSetting('core.secret', 'staticSalt')
                        . $this->password
                        . $this->salt );
    $this->level_id = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel()->level_id;

    if( empty($this->_modifiedFields['timezone']) )
    {
      $this->timezone = $settings->getSetting('tcore.locale.timezone', 'America/Los_Angeles');
    }
    if( empty($this->_modifiedFields['locale']) )
    {
      $this->locale = $settings->getSetting('core.locale.locale', 'auto');
    }
    if( empty($this->_modifiedFields['language']) )
    {
      $this->language = $settings->getSetting('core.locale.language', 'en_US');
    }

    $this->enabled =  (int) ($settings->getSetting('user.signup.approve', 1) == 1);
    $this->verified = (int) ($settings->getSetting('user.signup.verifyemail', 1) < 2);

    //    $this->invites_used = 0;
    $this->search = true;
    $this->creation_ip = ip2long($_SERVER['REMOTE_ADDR']);

    //$this->verify_code = md5($this->salt).uniqid(mt_rand(), true);
  }

  protected function _postInsert()
  {
    parent::_postInsert();
    
    // Create auth stuff
    $context = $this->api()->authorization()->context;
    
    // View
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $this, 'auth_view');
    if( empty($view_options) || !is_array($view_options) ) {
      $view_options = array('member', 'network', 'registered', 'everyone');
    }
    foreach( $view_options as $role ) {
      $context->setAllowed($this, $role, 'view', true);
    }
    
    // Comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $this, 'auth_comment');
    if( empty($comment_options) || !is_array($comment_options) ) {
      $comment_options = array('member', 'network', 'registered', 'everyone');
    }
    foreach( $comment_options as $role ) {
      $context->setAllowed($this, $role, 'comment', true);
    }
  }

  protected function _update()
  {
    // Hash password if being updated
    if( !empty($this->_modifiedFields['password']) ) {
      if( empty($user->salt) ) {
        $this->salt = (string) rand(1000000, 9999999);
      }
      $this->password = md5( Engine_Api::_()->getApi('settings', 'core')->getSetting('core.secret', 'staticSalt')
        . $this->password
        . $this->salt );
    }

    // Call parent
    parent::_update();
  }

  protected function _delete()
  {
    // Check level
    $level = Engine_Api::_()->getItem('authorization_level', $this->level_id);
    if( $level->flag == 'superadmin' ) {
      throw new User_Model_Exception('Cannot delete superadmins.');
    }
    
    // Remove from online users
    $table = $this->api()->getDbtable('online', 'user');
    $table->delete(array('user_id = ?' => $this->getIdentity()));

    // Remove fields values
    Engine_Api::_()->fields()->removeItemValues($this);

    // Call parent
    parent::_delete();
  }



  // Ownership

  public function isOwner(Core_Model_Item_Abstract $owner)
  {
    // A user only can be owned by self
    return ( $owner->getGuid(false) === $this->getGuid(false) );
  }

  public function getOwner()
  {
    // A user only can be owned by self
    return $this;
  }

  public function getParent()
  {
    // A user can only belong to self
    return $this;
  }



  // Blocking
  
  public function isBlocked($user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return false;
    }

    $table = $this->api()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $this->getIdentity())
      ->where('blocked_user_id = ?', $user->getIdentity())
      ->limit(1);
    $row = $table->fetchRow($select);
    return ( null !== $row );
  }

  public function isBlockedBy($user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return false;
    }
    
    $table = $this->api()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $user->getIdentity())
      ->where('blocked_user_id = ?', $this->getIdentity())
      ->limit(1);
    $row = $table->fetchRow($select);
    return ( null !== $row );
  }

  public function getBlockedUsers()
  {
    $user = Engine_Api::_()->user()->getViewer();
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return array();
    }
    
    $table = $this->api()->getDbtable('block', 'user');
    $select = $table->select()
      ->where('user_id = ?', $this->getIdentity());
    
    $ids = array();
    foreach( $table->fetchAll($select) as $row )
    {
      $ids[] = $row->blocked_user_id;
    }

    return $ids;
  }

  public function addBlock(User_Model_User $user)
  {
    // Check auth?
    //die(Engine_Api::_()->authorization()->isAllowed($user, $this, 'block'));
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return $this;
    }
    
    if( !$this->isBlocked($user) && $user->getGuid(false) != $this->getGuid(false) )
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try
      {
        $this->api()->getDbtable('block', 'user')
          ->insert(array(
            'user_id' => $this->getIdentity(),
            'blocked_user_id' => $user->getIdentity()
          ));
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }

    return $this;
  }

  public function removeBlock(User_Model_User $user)
  {
    // Check auth?
    if( !Engine_Api::_()->authorization()->isAllowed('user', $user, 'block') ) {
      return $this;
    }
    
    $this->api()->getDbtable('block', 'user')
      ->delete(array(
        'user_id = ?' => $this->getIdentity(),
        'blocked_user_id = ?' => $user->getIdentity()
      ));
      
    return $this;
  }



  // Interfaces

  /**
   * Gets a proxy object for the likes handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }

  /**
   * Gets a proxy object for the fields handler
   *
   * @return Engine_ProxyObject
   */
  public function fields()
  {
    return new Engine_ProxyObject($this, $this->api()->getApi('core', 'fields'));
  }

  /**
   * Gets a proxy object for the membership handler
   * 
   * @return Engine_ProxyObject
   */
  public function membership()
  {
    return new Engine_ProxyObject($this, $this->api()->getDbtable('membership', 'user'));
  }

  public function lists()
  {
    return new Engine_ProxyObject($this, $this->api()->getDbtable('lists', 'user'));
  }


  /**
   * Gets a proxy object for the fields handler
   *
   * @return Engine_ProxyObject
   */
  public function status()
  {
    return new Engine_ProxyObject($this, $this->api()->getDbtable('status', 'core'));
  }



  // Utility
  
  protected function _readData($spec)
  {
    if( is_scalar($spec) )
    {
      // Identity
      if( is_numeric($spec) )
      {
        // Can't use find because it won't return a row class
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("user_id = ?", $spec));
      }

      // By email
      else if( is_string($spec) && strpos($spec, '@') !== false )
      {
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("email = ?", $spec));
      }

      // By username
      else if( is_string($spec) )
      {
        $spec = $this->getTable()->fetchRow($this->getTable()->select()->where("username = ?", $spec));
      }
    }

    parent::_readData($spec);
  }

}
