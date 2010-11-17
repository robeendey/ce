<?php

abstract class Install_Import_Version3_AbstractFields extends Install_Import_Version3_Abstract
{
  protected $_toTableTruncate = false;

  protected $_fromResourceType;

  protected $_fromAlternateResourceType;

  protected $_toResourceType;

  protected $_useProfileType = false;

  
  protected $_profileTypeFieldIdentity;

  protected $_profileTypeOptionMap;

  protected $_profileTypeSubcatMap;

  protected $_profileTypeHeadingMap;

  protected $_profileFieldsMap;

  protected $_profileFieldsOptionsMap;

  protected $_profileDependentFieldsMap;

  protected $_profileOrderIndex;

  protected $_profileOptionOrderIndex;

  protected $_profileSearchColumns;

  protected $_profileSearchIndex;

  
  protected $_profileFieldCount = 0;

  protected $_profileOptionCount = 0;

  protected $_profileSearchCount = 0;
  
  public function __sleep()
  {
    return array_merge(parent::__sleep(), array(
      '_fromResourceType', '_fromAlternateResourceType', '_toResourceType',
      '_useProfileType',

      '_profileTypeFieldIdentity', '_profileTypeOptionMap',
      '_profileTypeSubcatMap', '_profileTypeHeadingMap', '_profileFieldsMap',
      '_profileFieldsOptionsMap', '_profileDependentFieldsMap',
      '_profileOrderIndex', '_profileOptionOrderIndex', '_profileSearchColumns',
      '_profileSearchIndex',
      
      '_profileFieldCount', '_profileOptionCount', '_profileSearchCount',
    ));
  }

  protected function _initPost()
  {
    $this->_fromTable = 'se_' . $this->_fromAlternateResourceType .'s';
    $this->_fromColumns = array(
      $this->_fromAlternateResourceType . '_id',
      $this->_fromAlternateResourceType . '_' . $this->_fromResourceType .'cat_id'
    );
    $this->_toTable = 'engine4_' . $this->_toResourceType . '_fields_values';
  }

  protected function _runPre()
  {
    // Step 1: Prepare data ----------------------------------------------------
    $fromDb = $this->getFromDb();
    $toDb = $this->getToDb();
    
    // Check all from tables exist
    $required_from_tables = array(
      'se_' . $this->_fromResourceType . 'cats',
      'se_' . $this->_fromResourceType . 'fields',
      'se_' . $this->_fromResourceType . 'values',
    );
    if( !$this->_tableExists($fromDb, $required_from_tables) ) {
      throw new Engine_Exception(sprintf('One of the source tables does not exist: %s', join(', ', $required_from_tables)));
    }

    // Check all to tables exist
    $required_to_tables = array(
      'engine4_' . $this->_toResourceType . '_fields_meta',
      'engine4_' . $this->_toResourceType . '_fields_maps',
      'engine4_' . $this->_toResourceType . '_fields_options',
      'engine4_' . $this->_toResourceType . '_fields_search',
      'engine4_' . $this->_toResourceType . '_fields_values',
    );
    if( !$this->_tableExists($toDb, $required_to_tables) ) {
      throw new Engine_Exception(sprintf('One of the target tables does not exist: %s', join(', ', $required_to_tables)));
    }
    
    // Truncate existing tables
    $toDb->query('TRUNCATE TABLE' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_meta'));
    $toDb->query('TRUNCATE TABLE' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_maps'));
    $toDb->query('TRUNCATE TABLE' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_options'));
    $toDb->query('TRUNCATE TABLE' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_search'));
    $toDb->query('TRUNCATE TABLE' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_values'));

    // Remove search columns
    $searchCols = $toDb->query('SHOW COLUMNS FROM ' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_search'))->fetchAll();
    foreach( $searchCols as $searchCol ) {
      if( $searchCol['Field'] != 'item_id' ) {
        try {
          $toDb->query('ALTER TABLE ' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_search')
            . ' DROP COLUMN ' . $toDb->quoteIdentifier($searchCol['Field']));
        } catch( Exception $e ) {
          $this->_warning($e->getMessage(), 0);
        }
      }
    }

    // Hack to prevent it running again
    $this->_runCount += 1;
    // -------------------------------------------------------------------------


    
    // Step 2: Import profile types --------------------------------------------

    // Create profile type field
    if( null === $this->_profileTypeFieldIdentity ) {

      $toDb->insert('engine4_' . $this->_toResourceType . '_fields_meta', array(
        'type' => 'profile_type',
        'label' => 'Profile Type',
        'alias' => 'profile_type',
        'required' => 1,
        'display' => 0,
        //'publish' => 0,
        'search' => 2,
        'order' => 1,
      ));
      $this->_profileTypeFieldIdentity = $toDb->lastInsertId();

      $toDb->insert('engine4_' . $this->_toResourceType . '_fields_maps', array(
        'field_id' => 0,
        'option_id' => 0,
        'child_id' => $this->_profileTypeFieldIdentity,
      ));

      $this->_profileFieldCount++;

    }

    // Add profile type options
    if( null === $this->_profileTypeOptionMap &&
        null === $this->_profileTypeSubcatMap &&
        null === $this->_profileTypeHeadingMap ) {

      $this->_profileTypeOptionMap = array();
      $this->_profileTypeSubcatMap = array();
      $this->_profileTypeHeadingMap = array();

      $stmt = $fromDb->select()
        ->from('se_' . $this->_fromResourceType . 'cats')
        ->order($this->_fromResourceType . 'cat_order')
        ->query();

      foreach( $stmt->fetchAll() as $data ) {
        // Don't add subcategories
        if( !empty($data[$this->_fromResourceType . 'cat_dependency']) ) {
          $this->_profileTypeSubcatMap[$data[$this->_fromResourceType . 'cat_id']] = $data[$this->_fromResourceType . 'cat_dependency'];
          $this->_profileTypeHeadingMap[$data[$this->_fromResourceType . 'cat_id']] = $data;
          continue;
        }
        $title = $this->_getLanguageValue($data[$this->_fromResourceType . 'cat_title']);
        $toDb->insert('engine4_' . $this->_toResourceType . '_fields_options', array(
          'field_id' => $this->_profileTypeFieldIdentity,
          'label' => $title,
        ));
        $this->_profileTypeOptionMap[$data[$this->_fromResourceType . 'cat_id']] = $toDb->lastInsertId();
      }
    }
    // -------------------------------------------------------------------------



    // Step 3: Import profile fields -------------------------------------------
    if( null === $this->_profileFieldsMap &&
        null === $this->_profileFieldsOptionsMap &&
        null === $this->_profileDependentFieldsMap &&
        null === $this->_profileOrderIndex &&
        null === $this->_profileOptionOrderIndex ) {

      $this->_profileFieldsMap = array();
      $this->_profileFieldsOptionsMap = array();
      $this->_profileDependentFieldsMap = array();
      $this->_profileOrderIndex = 1;
      $this->_profileOptionOrderIndex = 1;

      // Import profile fields
      $stmt = $fromDb->select()
        ->from('se_' . $this->_fromResourceType . 'fields')
        ->order($this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id ASC')
        ->order($this->_fromResourceType . 'field_dependency ASC')
        ->order($this->_fromResourceType . 'field_order ASC')
        ->query();

      foreach( $stmt->fetchAll() as $data ) {
        $title = $this->_getLanguageValue($data[$this->_fromResourceType . 'field_title']);
        $description = $this->_getLanguageValue($data[$this->_fromResourceType . 'field_desc']);
        $error = $this->_getLanguageValue($data[$this->_fromResourceType . 'field_error']);

        // Dependent field
        if( isset($this->_profileDependentFieldsMap[$data[$this->_fromResourceType . 'field_id']]) ) {
          $currentFieldParentFieldId = $this->_profileDependentFieldsMap[$data[$this->_fromResourceType . 'field_id']]['field_id'];
          $currentFieldParentOptionId = $this->_profileDependentFieldsMap[$data[$this->_fromResourceType . 'field_id']]['option_id'];
        }

        // Category
        else if( isset($this->_profileTypeOptionMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]) ) {
          $currentFieldParentFieldId = $this->_profileTypeFieldIdentity;
          $currentFieldParentOptionId = $this->_profileTypeOptionMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']];
        } else if( isset($this->_profileTypeSubcatMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]) &&
            isset($this->_profileTypeOptionMap[$this->_profileTypeSubcatMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]]) ) {
          $currentFieldParentFieldId = $this->_profileTypeFieldIdentity;
          $currentFieldParentOptionId = $this->_profileTypeOptionMap[$this->_profileTypeSubcatMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]];
        } else {
          $this->_error('Missing parent category in field: ' . $data[$this->_fromResourceType . 'field_id']);
          continue;
        }

        // Insert heading before this field
        if( isset($this->_profileTypeHeadingMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]) ) {
          $toDb->insert('engine4_' . $this->_toResourceType . '_fields_meta', array(
            'type' => 'heading',
            'label' => $this->_getLanguageValue($this->_profileTypeHeadingMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']][$this->_fromResourceType . 'cat_title']),
            'order' => $this->_profileOrderIndex++,
            'display' => 1, // 2?
          ));
          $headingId = $toDb->lastInsertId();
          $toDb->insert('engine4_' . $this->_toResourceType . '_fields_maps', array(
            'field_id' => $this->_profileTypeFieldIdentity,
            'option_id' => $currentFieldParentOptionId,
            'child_id' => $headingId,
          ));
          // Remove?
          unset($this->_profileTypeHeadingMap[$data[$this->_fromResourceType . 'field_' . $this->_fromResourceType . 'cat_id']]);
        }

        $options = null;
        $alias = null;
        switch( $data[$this->_fromResourceType . 'field_type'] ) {
          case 1: // TEXT FIELD
            if( $data[$this->_fromResourceType . 'field_special'] == 2 ) {
              $alias = 'first_name';
              $type = 'first_name';
            } else if( $data[$this->_fromResourceType . 'field_special'] == 3 ) {
              $alias = 'last_name';
              $type = 'last_name';
            } else {
              $type = 'text';
            }
            break;
          case 2: // TEXTAREA
            $type = 'textarea';
            break;
          case 3: // SELECT BOX
            $type = 'select';
            try {
              $options = $this->_unserialize($data[$this->_fromResourceType . 'field_options']);
            } catch( Exception $e ) {
              $options = null;
            }
            break;
          case 4: // RADIO BUTTON
            $type = 'radio';
            break;
          case 5: // DATE FIELD
            if( $data[$this->_fromResourceType . 'field_special'] == 1 ) {
              $alias = 'birthdate';
              $type = 'birthdate';
            } else {
              $type = 'date';
            }
            break;
          case 6: // CHECKBOXES
            $type = 'multi_checkbox';
            try {
              $options = $this->_unserialize($data[$this->_fromResourceType . 'field_options']);
            } catch( Exception $e ) {
              $options = null;
            }
            break;
          default:
            $this->_warning('Unknown field type: ' . $data[$this->_fromResourceType . 'field_type'] . ' for field: ' . $data[$this->_fromResourceType . 'field_id'], 0);
            continue;
            break;
        }

        $newData = array(
          'type' => (string) $type,
          'label' => (string) $title,
          'description' => (string) $description,
          'alias' => (string) $alias,
          'required' => $data[$this->_fromResourceType . 'field_required'],
          'display' => ( $data[$this->_fromResourceType . 'field_display'] ? 1 : 0 ), // ?
          //'publish' => 0, // ?
          'search' => ( !$data[$this->_fromResourceType . 'field_search'] ? 0 : ( in_array($type, array('profile_type', 'first_name', 'last_name'))) ? 2 : 1 ),
          'order' => $this->_profileOrderIndex++,

          'error' => $error,
          'style' => $data[$this->_fromResourceType . 'field_style'],
        );

        try {
          $toDb->insert('engine4_' . $this->_toResourceType . '_fields_meta', $newData);
        } catch( Exception $e ) {
          $this->_error($e);
          continue;
        }

        $fieldId = $toDb->lastInsertId();
        $this->_profileFieldsMap[$data[$this->_fromResourceType . 'field_id']] = $fieldId;

        // Do options
        if( !empty($options) && is_array($options) ) {
          $this->_profileFieldsOptionsMap[$data[$this->_fromResourceType . 'field_id']] = array();
          foreach( $options as $fieldOptionIndex => $option ) {
            $this->_profileOptionCount++;
            try {
              $toDb->insert('engine4_' . $this->_toResourceType . '_fields_options', array(
                'field_id' => $fieldId,
                'label' => $this->_getLanguageValue($option['label']),
                'order' => $this->_profileOptionOrderIndex++,
              ));
              $optionIdentity = $toDb->lastInsertId();
              $this->_profileFieldsOptionsMap[$data[$this->_fromResourceType . 'field_id']][$option['value']] = $optionIdentity;
            } catch( Exception $e ) {
              $this->_error($e);
              continue;
            }

            // Check for dependent field
            if( !empty($option['dependency']) || !empty($option['dependent_id']) || !empty($option['dependent_label']) ) {
              $this->_message('Dependent option: ' . Zend_Json::encode($option), 2);
              $this->_profileDependentFieldsMap[$option['dependent_id']] = array(
                'field_id' => $fieldId,
                'option_id' => $optionIdentity,
              );
            }
          }
        }

        // Do map
        try {
          $toDb->insert('engine4_' . $this->_toResourceType . '_fields_maps', array(
            'field_id' => $currentFieldParentFieldId,
            'option_id' => $currentFieldParentOptionId,
            'child_id' => $fieldId,
          ));
          $this->_profileFieldCount++;
        } catch( Exception $e ) {
          $this->_error($e);
          continue;
        }
      }
    }
    // -------------------------------------------------------------------------



    // Step 4: Import profile search structure ---------------------------------
    if( null === $this->_profileSearchColumns &&
        null === $this->_profileSearchIndex ) {

      $this->_profileSearchColumns = array();
      $this->_profileSearchIndex = array();

      $stmt = $toDb->select()
        ->from('engine4_' . $this->_toResourceType . '_fields_meta')
        ->where('search > ?', 0)
        ->query();

      foreach( $stmt->fetchAll() as $tmpFieldMeta ) {
        // Not searchable
        if( empty($tmpFieldMeta['search']) ) {
          continue;
        }

        $searchColName = null;
        if( !empty($tmpFieldMeta['alias']) ) {
          $searchColName = $tmpFieldMeta['alias'];
        } else {
          $searchColName = sprintf('field_%d', $tmpFieldMeta['field_id']);
        }

        // Already made?
        if( isset($this->_profileSearchColumns[$searchColName]) ) {
          continue;
        }

        $sql = 'ALTER TABLE ' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_search')
          . ' ADD COLUMN ' . $toDb->quoteIdentifier($searchColName);
        $keySql = 'ALTER TABLE ' . $toDb->quoteIdentifier('engine4_' . $this->_toResourceType . '_fields_search')
          . ' ADD INDEX (' . $toDb->quoteIdentifier($searchColName) . ')';
        switch( $tmpFieldMeta['type'] ) {
          case 'first_name':
          case 'last_name':
          case 'text':
            $sql .= ' varchar(255) NULL';
            break;
          case 'textarea':
            $sql .= ' varchar(255) NULL'; // ?
            break;

          case 'date':
          case 'birthdate':
            $sql .= ' DATETIME NULL';
            break;

          case 'profile_type':
          case 'select':
          case 'radio':
          case 'gender':
            $tmpFieldOptions = $toDb->select()
              ->from('engine4_' . $this->_toResourceType . '_fields_options', 'option_id')
              ->where('field_id = ?', $tmpFieldMeta['field_id'])
              ->query()
              ->fetchAll();
            if( empty($tmpFieldOptions) ) {
              continue 2;
            }
            $optStr = '';
            foreach( $tmpFieldOptions as $tmpFieldOption ) {
              if( $optStr != '' ) {
                $optStr .= ',';
              }
              $optStr .= sprintf("'%d'", $tmpFieldOption['option_id']); // blegh
            }
            $sql .= ' ENUM(' . $optStr . ') NULL';
            break;

          case 'multi_checkbox':
            $tmpFieldOptions = $toDb->select()
              ->from('engine4_' . $this->_toResourceType . '_fields_options', 'option_id')
              ->where('field_id = ?', $tmpFieldMeta['field_id'])
              ->query()
              ->fetchAll();
            if( empty($tmpFieldOptions) ) {
              continue 2;
            }
            $optStr = '';
            foreach( $tmpFieldOptions as $tmpFieldOption ) {
              if( $optStr != '' ) {
                $optStr .= ',';
              }
              $optStr .= sprintf("'%d'", $tmpFieldOption['option_id']); // blegh
            }
            $sql .= ' SET(' . $optStr . ') NULL';
            break;
          default:
            continue 2;
            break;
        }

        // Add column
        try {
          $toDb->query($sql);
        } catch( Exception $e ) {
          $this->_error($e->getMessage() . ', SQL was: ' . $sql);
          continue;
        }

        // Add as created
        $this->_profileSearchCount++;
        $this->_profileSearchColumns[$searchColName] = true;
        $this->_profileSearchIndex[$tmpFieldMeta['field_id']] = $searchColName;

        // Add key
        try {
          $toDb->query($keySql);
        } catch( Exception $e ) {
          $this->_error($e->getMessage() . ', SQL was: ' . $sql);
          continue;
        }

      }

    }
    // -------------------------------------------------------------------------
    
    
    
    $this->_message(sprintf('Imported %d profile fields', $this->_profileFieldCount));
    $this->_message(sprintf('Imported %d profile options', $this->_profileOptionCount));
    $this->_message(sprintf('Imported %d profile search columns', $this->_profileSearchCount));
  }
  
  protected function _translateRow(array $data, $key = null)
  {
    // Data should have two keys:
    //  $this->_fromAlternateResourceType . '_id'
    //  $this->_fromAlternateResourceType . '_' . $this->_fromResourceType .'cat_id'

    $fromDb = $this->getFromDb();
    $toDb = $this->getToDb();
    $resourceIdentity = @$data[$this->_fromAlternateResourceType . '_id'];
    $resourceProfileIdentity = @$data[$this->_fromAlternateResourceType . '_' . $this->_fromResourceType .'cat_id'];



    
    // Add profile type
    if( !$resourceProfileIdentity ) {
      $this->_warning('Missing profile type id: ' . $resourceProfileIdentity . ' for object: ' . $resourceIdentity);
      return false;
    }

    if( isset($this->_profileTypeOptionMap[$resourceProfileIdentity]) ) {
      $currentFieldParentOptionId = $this->_profileTypeOptionMap[$resourceProfileIdentity];
    } else if( isset($this->_profileTypeSubcatMap[$resourceProfileIdentity]) &&
        isset($this->_profileTypeOptionMap[$this->_profileTypeSubcatMap[$resourceProfileIdentity]]) ) {
      $currentFieldParentOptionId = $this->_profileTypeOptionMap[$this->_profileTypeSubcatMap[$resourceProfileIdentity]];
    } else {
      throw new Engine_Exception('Missing profile type id: ' . $resourceProfileIdentity . ' for object: ' . $resourceIdentity);
      return false;
    }
    
    $toDb->insert('engine4_' . $this->_toResourceType . '_fields_values', array(
      'item_id' => $resourceIdentity,
      'field_id' => $this->_profileTypeFieldIdentity,
      'value' => $currentFieldParentOptionId,
    ));



    // Get the user's profile values
    $profileValues = $fromDb->select()
      ->from('se_' . $this->_fromResourceType . 'values')
      ->where($this->_fromResourceType . 'value_' . $this->_fromAlternateResourceType .'_id' . ' = ?', $resourceIdentity)
      ->limit(1)
      ->query()
      ->fetch();

    // Nothing?
    if( !$profileValues || empty($profileValues) ) {
      return false;
    }
    
    // Import profile values
    $valId = $profileValues[$this->_fromResourceType . 'value_id'];
    $valUserId = $profileValues[$this->_fromResourceType . 'value_' . $this->_fromAlternateResourceType .'_id'];
    unset($profileValues[$this->_fromResourceType . 'value_id']);
    unset($profileValues[$this->_fromResourceType . 'value_' . $this->_fromAlternateResourceType .'_id']);

    $userDatum = array();
    $userDatum['item_id'] = $valUserId;
    
    foreach( $profileValues as $key => $value ) {
      $valFieldId = array_pop(explode('_', $key));
      // Missing field
      if( !isset($this->_profileFieldsMap[$valFieldId]) ) {
        $this->_message('No field for value: ' . $key);
        continue;
      }
      $valNewFieldId = $this->_profileFieldsMap[$valFieldId];
      // Value by option
      if( isset($this->_profileFieldsOptionsMap[$valFieldId]) ) {
        // Nothing selected
        if( $value == -1 || /* Not sure about empty */ empty($value) ) {
          continue;
        }

        // Normal select or multi_checkbox with one value selected
        else if( is_numeric($value) ) {
          if( isset($this->_profileFieldsOptionsMap[$valFieldId][$value]) ) {
            $valFieldValue = $this->_profileFieldsOptionsMap[$valFieldId][$value];
          } else {
            $this->_message('No corresponding value for field value: ' . $value . ' for field id: ' . $valFieldId);
            continue;
          }
        }

        // Multi select?
        else if( strpos($value, ',') !== false &&
            ($value = array_filter(array_map('trim', explode(',', $value)))) &&
            is_array($value) &&
            !empty($value) ) {
          $valFieldValue = array();
          foreach( $value as $valueArrVal ) {
            if( isset($this->_profileFieldsOptionsMap[$valFieldId][$valueArrVal]) ) {
              $valFieldValue[] = $this->_profileFieldsOptionsMap[$valFieldId][$valueArrVal];
            } else {
              $this->_message('No corresponding value for multi select field value: ' . $valueArrVal . ' for field id: ' . $valFieldId);
            }
          }
        }

        // Unknown
        else {
          $this->_message('Unknown option value type for field value: ' . $value . ' for field id: ' . $valFieldId);
          continue;
        }
      }
      // Value by value
      else {
        $valFieldValue = $value;
      }

      // Insert
      if( null !== $valFieldValue ) {
        // multi select
        if( is_array($valFieldValue) ) {
          if( !empty($valFieldValue) ) {
            $tmpValFieldValueIndex = 0;
            if( isset($this->_profileSearchIndex[$valNewFieldId]) ) {
              $userDatum[$this->_profileSearchIndex[$valNewFieldId]] = array();
            }
            foreach( $valFieldValue as $valFieldValueIn ) {
              if( isset($this->_profileSearchIndex[$valNewFieldId]) ) {
                $userDatum[$this->_profileSearchIndex[$valNewFieldId]][] = $valFieldValueIn;
              }
              try {
                $toDb->insert('engine4_' . $this->_toResourceType . '_fields_values', array(
                  'item_id' => $valUserId,
                  'field_id' => $valNewFieldId,
                  'index' => $tmpValFieldValueIndex++,
                  'value' => $valFieldValueIn,
                ));
              } catch( Exception $e ) {
                $this->_error($e);
              }
            }
          }
        }

        // Scalar
        else {
          if( isset($this->_profileSearchIndex[$valNewFieldId]) ) {
            $userDatum[$this->_profileSearchIndex[$valNewFieldId]] = $valFieldValue;
          }
          try {
            $toDb->insert('engine4_' . $this->_toResourceType . '_fields_values', array(
              'item_id' => $valUserId,
              'field_id' => $valNewFieldId,
              'value' => $valFieldValue,
            ));
          } catch( Exception $e ) {
            $this->_error($e);
          }
        }
      }
    }



    // Add fields search values
    if( !empty($userDatum) && is_array($userDatum) && count($userDatum) > 1 ) {
      try {
        foreach( $userDatum as $k => $v ) {
          if( is_array($v) ) {
            $userDatum[$k] = $toDb->quote($v);
          }
        }
        $toDb->insert('engine4_' . $this->_toResourceType . '_fields_search', $userDatum);
      } catch( Exception $e ) {
        $this->_error($e);
      }
    }

    return true;
  }
}