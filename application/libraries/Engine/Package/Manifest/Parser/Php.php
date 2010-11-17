<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Package
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Php.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_Filter
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_Package_Manifest_Parser_Php extends Engine_Package_Manifest_Parser
{
  public function toString($arr)
  {
    return $this->format(var_export($arr, true));
  }

  public function fromString($string)
  {
    throw new Engine_Package_Manifest_Exception('fromString() not supported for PHP files');
  }
  
  public function toFile($filename, $arr)
  {
    if( strtolower(substr($filename, -4)) != '.php' ) {
      throw new Engine_Package_Manifest_Exception('Invalid file extension');
    }

    $data = $this->toString($arr);

    if( !file_put_contents($filename, $data) ) {
      throw new Engine_Package_Manifest_Exception('Unable to write data to file');
    }
  }

  public function fromFile($filename)
  {
    if( !file_exists($filename) ) {
      throw new Engine_Package_Manifest_Exception('Missing file');
    }

    if( strtolower(substr($filename, -4)) != '.php' ) {
      throw new Engine_Package_Manifest_Exception('Invalid file extension');
    }

    $arr = include $filename;
    //$arr = $this->fromString(simplexml_load_file($filename));

    if( empty($arr) ) {
      throw new Engine_Package_Manifest_Exception('Unable to load data');
    }

    return $arr;
  }

  public function format($string)
  {
    return $string; // Already formatted from var_export
  }
}