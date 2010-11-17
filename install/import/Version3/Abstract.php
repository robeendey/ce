<?php

abstract class Install_Import_Version3_Abstract extends Install_Import_DbAbstract
{
  static protected $_defaultLanguageIdentity;

  static protected $_levelMap = array();

  protected function _getDefaultLanguageIdentity()
  {
    if( null === self::$_defaultLanguageIdentity ) {
      self::$_defaultLanguageIdentity = $this->getFromDb()->select()
        ->from('se_languages', 'language_id')
        ->where('language_default = ?', 1)
        ->limit(1)
        ->order('language_id ASC')
        ->query()
        ->fetchColumn(0)
        ;
      if( !self::$_defaultLanguageIdentity ) {
        self::$_defaultLanguageIdentity = false;
      }
    }
    return self::$_defaultLanguageIdentity;
  }

  protected function _getLanguageValue($key, $language = null)
  {
    if( !$language ) {
      $language = $this->_getDefaultLanguageIdentity();
    }
    return $this->getFromDb()->select()
      ->from('se_languagevars', 'languagevar_value')
      ->where('languagevar_id = ?', $key)
      ->where('languagevar_language_id = ?', $language)
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;
  }

  protected function _setLevelMap($oldLevelId, $newLevelId)
  {
    self::$_levelMap[$oldLevelId] = $newLevelId;
    if( ($cache = $this->getCache()) instanceof Zend_Cache_Core ) {
      $cache->save(self::$_levelMap, 'levelmap');
    }
    return $this;
  }

  protected function _getLevelMap($oldLevelId)
  {
    if( empty(self::$_levelMap) ) {
      if( ($cache = $this->getCache()) instanceof Zend_Cache_Core ) {
        self::$_levelMap = $cache->load('levelmap');
      }
    }

    return @self::$_levelMap[$oldLevelId];
  }



  // Utility

  protected function _translateCommaStringToArray($string)
  {
    $array = explode(',', $string);
    $array = array_map('trim', $array);
    $array = array_filter($array);
    return $array;
  }

  protected function _translatePrivacy($value, $mode = null)
  {
    $arr = array();

    if( $value & 32 ) {
      $arr[] = 'everyone';
    }

    if( $value & 16 ) {
      $arr[] = 'registered';
    }

    if( $value & 8 ) {
      $arr[] = 'network';
    }

    if( $value & 4 ) {
      if( $mode == 'owner' ) {
        $arr[] = 'owner_member_member';
      } else if( $mode == 'parent' ) {
        $arr[] = 'parent_member_member';
      } else {
        $arr[] = 'member_member';
      }
    }

    if( $value & 2 ) {
      if( $mode == 'owner' ) {
        $arr[] = 'owner_member';
      } else if( $mode == 'parent' ) {
        $arr[] = 'parent_member';
      } else {
        $arr[] = 'member';
      }
    }

    //if( $value & 1 ) {
    //  $arr[] = 'owner';
    //}

    return $arr;
  }

  protected function _translatePrivacyPermission($values, $mode = null)
  {
    if( is_string($values) ) {
      $values = unserialize($values);
    }
    if( !is_array($values) ) {
      return array();
    }

    $arr = array();

    if( in_array('63', $values) ) {
      $arr[] = 'everyone';
    }
    if( in_array('31', $values) ) {
      $arr[] = 'registered';
    }
    if( in_array('15', $values) ) {
      if( $mode == 'parent' ) {
        $arr[] = 'parent_network';
      } else if( $mode == 'owner' ) {
        $arr[] = 'owner_network';
      } else {
        $arr[] = 'network';
      }
    }
    if( in_array('7', $values) ) {
      if( $mode == 'parent' ) {
        $arr[] = 'parent_member_member';
      } else if( $mode == 'owner' ) {
        $arr[] = 'owner_member_member';
      } else {
        $arr[] = 'member_member';
      }
    }
    if( in_array('3', $values) ) {
      if( $mode == 'parent' ) {
        $arr[] = 'parent_member';
      } else if( $mode == 'owner' ) {
        $arr[] = 'owner_member';
      } else {
        $arr[] = 'member';
      }
    }
    if( in_array('1', $values) ) {
      $arr[] = 'owner';
    }

    return $arr;
  }

  protected function _translateGroupPrivacyPermission($values, $mode = null)
  {
    if( is_string($values) ) {
      $values = unserialize($values);
    }
    if( !is_array($values) ) {
      return array();
    }

    $arr = array();

    if( in_array('255', $values) ) {
      $arr[] = 'everyone';
    }
    if( in_array('127', $values) ) {
      $arr[] = 'registered';
    }
    if( in_array('63', $values) || in_array('31', $values) || in_array('15', $values) || in_array('7', $values) ) {
      $arr[] = 'member';
    }
    $arr[] = 'officer';
    $arr[] = 'owner';

    return $arr;
  }

  protected function _translateEventPrivacyPermission($values, $mode = null)
  {
    if( is_string($values) ) {
      $values = unserialize($values);
    }
    if( !is_array($values) ) {
      return array();
    }

    $arr = array();

    if( in_array('127', $values) ) {
      $arr[] = 'everyone';
    }
    if( in_array('63', $values) ) {
      $arr[] = 'registered';
    }
    if( in_array('31', $values) || in_array('15', $values) || in_array('7', $values) || in_array('3', $values) ) {
      $arr[] = 'member';
    }
    $arr[] = 'owner';

    return $arr;
  }

  protected function _translateEventPrivacy($value, $mode = null)
  {
    $arr = array();

    if( $value & 64 ) {
      $arr[] = 'everyone';
    }

    if( $value & 32 ) {
      $arr[] = 'registered';
    }

    if( $value & 2 ) {
      $arr[] = 'member';
    }

    //if( $value & 1 ) {
    //  $arr[] = 'owner';
    //}

    return $arr;
  }

  protected function _translateGroupPrivacy($value, $mode = null)
  {
    $arr = array();

    if( $value & 128 ) {
      $arr[] = 'everyone';
    }

    if( $value & 64 ) {
      $arr[] = 'registered';
    }

    if( $value & 4 ) {
      $arr[] = 'member';
    }

    if( $value & 2 ) {
      $arr[] = 'officer';
    }

    //if( $value & 1 ) {
    //  $arr[] = 'owner';
    //}

    return $arr;
  }

  protected function _insertPrivacy($resourceType, $resourceId, $action, $roles)
  {
    if( is_string($roles) ) {
      $roles = array($roles);
    } else if( is_array($roles) ) {
      $roles = array_filter($roles, 'is_string');
    }
    if( !is_array($roles) || empty($roles) ) {
      return;
    }
    
    foreach( $roles as $role ) {
      try {
        $this->getToDb()->insert('engine4_authorization_allow', array(
          'resource_type' => $resourceType,
          'resource_id' => $resourceId,
          'action' => $action,
          'role' => $role,
          'value' => 1,
        ));
      } catch( Exception $e ) {
        $this->_error('Problem adding privacy options for object id ' . $resourceId . ' : ' . $e->getMessage());
      }
    }
  }

  protected function _insertSearch($type, $id, $title, $description = null, $keywords = null)
  {
    $title = trim(strip_tags((string) $title));
    $description = trim(strip_tags((string) $description));
    $keywords = trim(strip_tags((string) $keywords));
    
    if( empty($type) || empty($id) || ('' == $title && '' == $description && '' == $keywords) ) {
      return;
    }
    
    try {
      $this->getToDb()->insert('engine4_core_search', array(
        'type' => (string) $type,
        'id' => (integer) $id,
        'title' => (string) $title,
        'description' => (string) $description,
        'keywords' => (string) $keywords,
      ));
    } catch( Exception $e ) {
      $this->_log($e, Zend_Log::WARN);
    }
  }

  protected function _getFromUserDir($id, $prefix, $suffix = '')
  {
    $dir  = $this->getFromPath() . DIRECTORY_SEPARATOR;
    $dir .= $prefix . DIRECTORY_SEPARATOR;
    $dir .= sprintf('%d', $id + 999 - (($id - 1) % 1000)) . DIRECTORY_SEPARATOR;
    $dir .= $id;
    if( $suffix ) {
      $dir .= DIRECTORY_SEPARATOR . $suffix;
    }
    return $dir;
  }
}