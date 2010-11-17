<?php

// @todo remove

class Fields_View_Helper_FieldMultiselect extends Zend_View_Helper_Abstract
{
  public function fieldMultiselect($subject, $field, $value)
  {
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
