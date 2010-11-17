<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldLocation.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldLocation extends Zend_View_Helper_Abstract
{
  public function fieldLocation($subject, $field, $value)
  {
    return $value->value
      . ' ['
      . $this->view->htmlLink('http://maps.google.com/?q=' . urlencode($value->value), $this->view->translate('map'), array('target' => '_blank'))
      . ']';
  }
}