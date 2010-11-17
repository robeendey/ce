<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Fields.php 7462 2010-09-24 03:15:46Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Signup_Fields extends Fields_Form_Standard
{
  protected $_fieldType = 'user';

  public function init()
  {
    $this
      ->setIsCreation(true)
      ->setItem(Engine_Api::_()->user()->getUser(null));
    parent::init();
  }
}