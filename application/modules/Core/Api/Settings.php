<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Settings.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Api_Settings extends Core_Api_Abstract
{
  protected $_config;

  protected $_rowset;
  
  public function __construct()
  {
    $this->_loadSettings();
  }

  public function __get($key)
  {
    return $this->getSetting($key);
  }

  public function __set($key, $value)
  {
    return $this->setSetting($key, $value);
  }

  public function __isset($key)
  {
    return $this->hasSetting($key);
  }

  public function __unset($key)
  {
    return $this->removeSetting($key);
  }

  public function getSetting($key, $default = null)
  {
    $key = $this->_normalizeMagicProperty($key);
    $path = explode('.', $key);
    $value = $this->_getConfigKey($path);
    if( null === $value )
    {
      return $default;
    }
    if( $value instanceof Zend_Config )
    {
      return $value->toArray();
    }
    return $value;
  }

  public function getFlatSetting($key, $default = null, $flatChar = '_')
  {
    $value = $this->getSetting($key, $default);
    if( is_array($value) ) {
      $this->_flattenArray($value, $flatChar);
    }
    return $value;
  }

  public function setSetting($key, $value)
  {
    $key = $this->_normalizeMagicProperty($key);

    // Array handling
    if( is_array($value) )
    {
      foreach( $value as $valKey => $valValue )
      {
        $this->setSetting($key.'.'.$valKey, $valValue);
      }
      return $this;
    }

    // Set in config
    $path = explode('.', $key);
    $this->_setConfigKey($path, $value);

    // Set in db
    $row = $this->_rowset->getRowMatching('name', $key);

    // Delete setting
    if( null === $value )
    {
      // Delete current setting
      if( null !== $row ) {
        $row->delete();
      }
      // Delete rows like current setting
      $this->_rowset->getTable()->delete(array(
        'name LIKE ?' => $key.'.%'
      ));
    }

    // Set setting
    else
    {
      if( null === $row )
      {
        $row = $this->_rowset->getTable()->createRow();
        $row->name = $key;
      }
      $row->value = $value;
      $row->save();
    }
    
    return $this;
  }

  public function hasSetting($key)
  {
    $key = $this->_normalizeMagicProperty($key);
    $path = explode('.', $key);
    return ( null !== $this->_getConfigKey($path) );
  }

  public function removeSetting($key)
  {
    $key = $this->_normalizeMagicProperty($key);
    return $this->setSetting($key, null);
  }



  // Utility
  
  protected function _loadSettings()
  {
    $table = $this->api()->getDbtable('settings');
    $this->_rowset = $rowset = $table->fetchAll();
    $data = array();
    foreach( $rowset as $row )
    {
      $this->_expandArray(explode('.', $row->name), $row->value, $data);
    }
    $this->_config = new Zend_Config($data, true);
  }

  protected function _normalizeMagicProperty($key)
  {
    return strtolower(str_replace('_', '.', $key));
  }

  protected function _expandArray(array $path, $value, array &$array)
  {
    $current =& $array;
    foreach( $path as $pathElement )
    {
      if( !isset($current[$pathElement]) || !is_array($current[$pathElement]) )
      {
        $current[$pathElement] = array();
      }
      $current =& $current[$pathElement];
    }
    $current = $value;
  }

  protected function _getConfigKey(array $path)
  {
    $current = $this->_config;
    $last = array_pop($path);

    foreach( $path as $pathElement )
    {
      if( !isset($current->$pathElement) )
      {
        return;
      }
      $current = $current->$pathElement;
      if( !($current instanceof Zend_Config) )
      {
        return;
      }
    }

    if( !isset($current->$last) )
    {
      return;
    }

    return $current->$last;
  }

  protected function _setConfigKey(array $path, $value)
  {
    $current = $this->_config;
    $last = array_pop($path);

    foreach( $path as $pathElement )
    {
      $next = $current->$pathElement;
      if( !isset($next) || !($next instanceof Zend_Config) )
      {
        // Just return if unsetting
        if( null === $value )
        {
          return;
        }
        $current->$pathElement = array();
      }
      $current = $current->$pathElement;
    }

    if( null === $value )
    {
      unset($current->$last);
    }
    else
    {
      $current->$last = $value;
    }
  }

  protected function _resolveConfig(array $path, $config, $value = null)
  {
    $current = $config;
    foreach( $path as $pathElement )
    {
      if( !($current instanceof Zend_Config) )
      {
        return;
      }
      if( !isset($current->$pathElement) )
      {
        return;
      }
      $current = $current->$pathElement;
    }

    if( null != $value )
    {
      $current = $value;
    }

    return $current;
  }

  protected function _flattenArray(&$array, $char = '_')
  {
    do {
      $break = true;
      foreach( $array as $key => $value ) {
        if( is_array($value) ) {
          foreach( $value as $subkey => $subvalue ) {
            $newKey = $key . $char . $subkey;
            $array[$newKey] = $subvalue;
          }
          unset($array[$key]);
          $break = false;
        }
      }
    } while( !$break );
  }
}