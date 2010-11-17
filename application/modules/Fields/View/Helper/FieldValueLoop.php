<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldValueLoop.php 7612 2010-10-08 20:07:19Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldValueLoop extends Zend_View_Helper_Abstract
{
  public function fieldValueLoop($subject, $partialStructure)
  {
    if( empty($partialStructure) ) {
      return '';
    }

    if( !($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity() ) {
      return '';
    }
    
    // Generate
    $content = '';
    $lastContents = '';
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");
    
    foreach( $partialStructure as $map ) {

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($subject);
      if( !$field || $field->type == 'profile_type' || !$field->display ) continue;
      
      // Heading
      if( $field->type == 'heading' ) {
        if( !empty($lastContents) ) {
          $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->view->translate($field->label);
      }
      
      // Normal fields
      else
      {
        $tmp = $this->getFieldValueString($field, $value, $subject);
        if( !empty($tmp) ) {
          
          $label = $this->view->translate($field->label);
          $lastContents .= <<<EOF
  <li>
    <span>
      {$label}
    </span>
    <span>
      {$tmp}
    </span>
  </li>
EOF;
        }


         $lastContents .= '';
        $lastContents;
      }
      
    }

    if( !empty($lastContents) ) {
      $content .= $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject)
  {
    $tmp = null;
    if( (is_object($value) && isset($value->value)) || is_array($value) ) {
      if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);
      $helper = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
      //$helper = 'field' . Engine_Api::_()->fields()->inflectFieldType($field->type);
      if( $helper ) {
        $tmp = $this->view->$helper($subject, $field, $value);
      }
    }
    return $tmp;
  }

  protected function _buildLastContents($content, $title)
  {
    if( !$title ) {
      return '<ul>' . $content . '</ul>';
    }
    return <<<EOF
        <div class="profile_fields">
          <h4>
            <span>{$title}</span>
          </h4>
          <ul>
            {$content}
          </ul>
        </div>
EOF;
  }
}