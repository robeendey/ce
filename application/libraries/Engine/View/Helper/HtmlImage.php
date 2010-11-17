<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: HtmlImage.php 7244 2010-09-01 01:49:53Z john $
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_HtmlImage extends Zend_View_Helper_HtmlElement
{
  public function htmlImage($src, $alt = "", $attribs = array())
  {
    // Allow passing an array
    if( is_array($src) )
    {
      $route = ( isset($src['route']) ? $src['route'] : 'default' );
      $reset = ( isset($src['reset']) ? $src['reset'] : false );
      unset($src['route']);
      unset($src['reset']);
      $src = $this->view->url($src, $route, $reset);
    }

    // Merge data and type
    $attribs = array_merge(array(
        'src' => $src,
        'alt' => $alt), $attribs);

    $closingBracket = $this->getClosingBracket();

    return '<img'.$this->_htmlAttribs($attribs).$closingBracket;
  }
}