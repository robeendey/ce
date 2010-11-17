<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: ViewMore.php 7481 2010-09-27 08:41:01Z john $
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_ViewMore extends Zend_View_Helper_HtmlElement
{
  //static protected $_index = 1;
  
  protected $_moreLength = 255; // Note: truncation at 255 + 4 = 259 (for " ...")
  protected $_maxLength = 1027;
  protected $_fudgesicles = 10;
  //protected $_prefix = 'viewmore';
  protected $_tag = 'span';

  public function viewMore($string, $moreLength = null, $maxLength = null)
  {
    if( !is_numeric($moreLength) || $moreLength <= 0 ) {
      $moreLength = $this->_moreLength;
    }
    if( !is_numeric($maxLength) || $maxLength <= 0 ) {
      $maxLength = $this->_maxLength;
    }

    if( Engine_String::strlen($string) <= $moreLength + $this->_fudgesicles )
    {
      return $string;
    }
    if( Engine_String::strlen($string) >= $maxLength )
    {
      $string = Engine_String::substr($string, 0, $maxLength) . $this->view->translate('... &nbsp;');
    }

    //$id = $this->_prefix . '_' . self::$_index++;
    //$class = $this->_prefix;
    $content = '';

    $content .= '<'
      . $this->_tag
      . ' class="view_more"'
      . '>'
      . Engine_String::substr($string, 0, $moreLength) . $this->view->translate('... &nbsp;')
      . '<a class="view_more_link" href="javascript:void(0);" onclick="$(this).getParent().getNext().style.display=\'\';$(this).getParent().style.display=\'none\';">'.$this->view->translate('more').'</a>'
      . '</'
      . $this->_tag
      . '>'
      . '<'
      . $this->_tag
      . ' class="view_more"'
      . ' style="display:none;"'
      . '>'
      . $string . ' &nbsp;'
      . '<a class="view_less_link" href="javascript:void(0);" onclick="$(this).getParent().getPrevious().style.display=\'\';$(this).getParent().style.display=\'none\';">'.$this->view->translate('less').'</a>'
      . '</'
      . $this->_tag
      . '>'
      ;

    return $content;
  }

  public function setMoreLength($length)
  {
    if( is_numeric($length) && $length > 0 )
    {
      $this->_moreLength = $length;
    }

    return $this;
  }

  public function setMaxLength($length)
  {
    if( is_numeric($length) && $length > 0 )
    {
      $this->_maxLength = $length;
    }

    return $this;
  }
}