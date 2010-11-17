<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Translate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Array.php 7244 2010-09-01 01:49:53Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_Translate
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Translate_Parser_Array implements Engine_Translate_Parser_Interface
{
  public static function parse($file, $locale = null, array $options = array())
  {
    $data = array();
    if( is_array($file) )
    {
      $data[$locale] = $file;
    }
    else if( is_string($file) && file_exists($file) )
    {
      ob_start();
      $data[$locale] = include($file);
      ob_end_clean();
    }

    if( !is_array($data[$locale]) )
    {
      require_once 'Zend/Translate/Exception.php';
      throw new Zend_Translate_Exception("Error including array or file '".$data."'");
    }

    return $data;
  }
}
