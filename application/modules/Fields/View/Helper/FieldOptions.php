<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldOptions.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldOptions extends Zend_View_Helper_Abstract
{
  public function fieldOptions($subject, $field, $value)
  {
    $info = Engine_Api::_()->fields()->getFieldInfo($field->type, 'multiOptions');

    // Single
    if( is_object($value) )
    {
      if( isset($info[$value->value]) ) {
        return $info[$value->value];
      }
    }

    // Multi
    else if( is_array($value) )
    {
      $first = true;
      $content = '';
      foreach( $value as $sVal ) {
        if( !isset($info[$sVal->value]) ) continue;
        if( !$first ) $content .= ', ';
        $content .= $info[$sVal->value];
        $first = false;
      }
      return $content;
    }

    return '';
  }
}