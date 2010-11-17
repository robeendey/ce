<?php

abstract class Install_Import_Version3_AbstractTags extends Install_Import_Version3_Abstract
{
  protected $_fromResourceType = 'eventmedia';

  protected $_toResourceType = 'event_photo';

  protected $_toTableTruncate = false;

    /* Moved to CleanupPre
  static protected $_toTableTruncated = false;
    */

  public function __sleep()
  {
    return array_merge(parent::__sleep(), array(
      '_fromResourceType', '_toResourceType' //, '_toTableTruncated', // That last one might not work
    ));
  }

  protected function _initPre()
  {
    $this->_fromTable = 'se_' . $this->_fromResourceType . 'tags';
    $this->_toTable = 'engine4_core_tagmaps';

    /* Moved to CleanupPre
    // Do only once
    if( !self::$_toTableTruncated ) {
      $this->getToDb()->query('TRUNCATE TABLE ' . $this->getToDb()->quoteIdentifier($this->getToTable()));
      self::$_toTableTruncated = true;
    }
    */
  }

  protected function _translateRow(array $data, $key = null)
  {
    $fromType = $this->_fromResourceType;
    $toType = $this->_toResourceType;
    
    $newData = array(
      // Resource
      'resource_type' => $toType,
      'resource_id' => $data[$fromType . 'tag_' . $fromType . '_id'],
      // Assume tagger is resource?
      'tagger_type' => $toType,
      'tagger_id' => $data[$fromType . 'tag_' . $fromType . '_id'],
    );

    // Text tag
    if( empty($data[$fromType . 'tag_user_id']) ) {
      $existingTagId = $this->getToDb()->select()
        ->from('engine4_core_tags', 'tag_id')
        ->where('text = ?', $this->_formatTagText($data[$fromType . 'tag_text']))
        ->limit(1)
        ->query()
        ->fetchColumn(0)
        ;

      if( !$existingTagId ) {
        $this->getToDb()->insert('engine4_core_tags', array(
          'text' => $this->_formatTagText($data[$fromType . 'tag_text']),
        ));
        $existingTagId = $this->getToDb()->lastInsertId();
      }

      $newData['tag_type'] = 'core_tag';
      $newData['tag_id'] = $existingTagId;
    }

    // User tag
    else {
      $newData['tag_type'] = 'user';
      $newData['tag_id'] = $data[$fromType . 'tag_user_id'];
    }

    // Extra
    if( isset($data[$fromType . 'tag_x']) ) {
      $newData['extra'] = Zend_Json::encode(array(
        'x' => $data[$fromType . 'tag_x'],
        'y' => $data[$fromType . 'tag_y'],
        'w' => $data[$fromType . 'tag_width'],
        'h' => $data[$fromType . 'tag_height'],
      ));
    }

    // Date
    //$newData['creation_date'] = $this->_translateTime($data[$fromType . 'tag_date']);

    return $newData;
  }

  protected function _formatTagText($text)
  {
    // We can do formatting on tags later
    return trim($text);
  }
}
