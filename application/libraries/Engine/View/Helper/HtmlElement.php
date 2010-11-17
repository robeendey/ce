<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: HtmlElement.php 7244 2010-09-01 01:49:53Z john $
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_HtmlElement extends Zend_View_Helper_HtmlElement
{
  protected $_ignoreOnEmptyContent = true;

  public function htmlElement($tag)
  {
    if( empty($tag) ) {
      return '';
    }
    
    $args = func_get_args();
    if( count($args) > 3 ) {
      throw new Zend_View_Exception('htmlElement() only accepts a max of 3 arguments.');
    }
    array_shift($args);

    $content = '';
    $attribs = array();
    foreach( $args as $arg ) {
      if( is_array($arg) ) {
        $attribs = $arg;
      } else if( is_string($arg) ) {
        $content = $arg;
      }
    }
    

    // Empty content
    if( empty($content) ) {
      $closingBracket = $this->getClosingBracket();
      return '<' . $tag . $this->_htmlAttribs($attribs) . $closingBracket;
    }

    // Normal content
    return '<' . $tag
      . $this->_htmlAttribs($attribs)
      . '>'
      . $content
      . '</'
      . $tag
      . '>';
  }
}