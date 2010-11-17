<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FieldFacebook.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John
 */
class Fields_View_Helper_FieldFacebook extends Zend_View_Helper_Abstract
{
  public function fieldFacebook($subject, $field, $value)
  {
    $facebookUrl = stripos($value->value, 'facebook.com/') === false
                 ? 'http://www.facebook.com/search/?q=' . $value->value
                 : $value->value;
    return $this->view->htmlLink($facebookUrl, $value->value, array(
      'target' => '_blank',
      'ref' => 'nofollow',
    ));
  }
}