<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Content.php 7244 2010-09-01 01:49:53Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_Content extends Zend_View_Helper_Abstract
{
  /**
   * Name of current area
   * 
   * @var string
   */
  protected $_name;

  /**
   * Render a content area by name
   * 
   * @param string $name
   * @return string
   */
  public function content($name = null)
  {
    // Direct access
    if( func_num_args() == 0 )
    {
      return $this;
    }

    if( func_num_args() > 1 )
    {
      $name = func_get_args();
    }

    $content = Engine_Content::getInstance();

    return $content->render($name);
  }

  public function renderWidget($name)
  {
    $structure = array(
      'type' => 'widget',
      'name' => $name,
    );

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
      'elements' => array($structure),
      'decorators' => array(
        'Children'
      )
    ));

    return $element->render();
  }
}