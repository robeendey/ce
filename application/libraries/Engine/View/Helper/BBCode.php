<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: BBCode.php 7244 2010-09-01 01:49:53Z john $
 * @todo       documentation
 */

require_once('PEAR.php');

require_once('HTML/BBCodeParser.php');

/**
 * @category   Engine
 * @package    Engine_View
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_View_Helper_BBCode extends Zend_View_Helper_Abstract
{
  protected $_filters = array(
    'Basic',
    'Extended',
    'Links',
    'Images',
    'Lists',
    'Email'
  );
  
  public function BBCode($text)
  {
    $parser = new HTML_BBCodeParser(array(
      'filters' => join(',', $this->_filters)
    ));
    $parser->setText($text);
    $parser->parse();
    return $parser->getParsed();
  }

  public function addFilter($name)
  {
    $name = ucfirst($name);
    $this->_filters = array_unique(array_merge($this->_filters, array($name)));
    return $this;
  }

  public function removeFilter($name)
  {
    $name = ucfirst($name);
    $this->_filters = array_diff($this->_filters, array($name));
    return $this;
  }
}