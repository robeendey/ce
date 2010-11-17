<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldSelect.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldSelect extends Zend_View_Helper_Abstract
{
  public function fieldSelect($subject, $field, $value)
  {
    if( is_object($value) ) {
      $selectedOption = Engine_Api::_()->fields()->getFieldsOptions($field->getFieldType())->getRowMatching('option_id', $value->value);
      if( !$selectedOption || $selectedOption->field_id != $field->field_id ) {
        return '';
      }

      return $this->view->translate($selectedOption->label);
    }

    else if( is_array($value) ) {

      // Build values
      $vals = array();
      foreach( $value as $singleValue ) {
        if( is_string($singleValue) ) {
          $vals[] = $singleValue;
        } else if( is_object($singleValue) ) {
          $vals[] = $singleValue->value;
        }
      }

      $options = $field->getOptions();
      $first = true;
      $content = '';
      foreach( $options as $option ) {
        if( !in_array($option->option_id, $vals) ) continue;
        if( !$first ) $content .= ', ';
        $content .= $this->view->translate($option->label);
        $first = false;
      }

      return $content;
      
    }
  }
}