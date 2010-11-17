<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Vfs
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: System.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

//require_once 'Engine/Vfs/Object/Abstract.php';
//require_once 'Engine/Vfs/Object/Exception.php';

/**
 * @category   Engine
 * @package    Engine_Vfs
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_Vfs_Object_System extends Engine_Vfs_Object_Abstract
{
  public function open($mode = 'r')
  {
    $resource = fopen($this->getPath(), $mode);
    if( !$resource ) {
      throw new Engine_Vfs_File_Exception(sprintf('Unable to open file "%s" in mode "%s"', $filename, $mode));
    }
    
    $this->_resource = $resource;
    return $this;
  }

  public function close()
  {
    $ret = fclose($this->getResource());
    $this->_resource = null;
    return $ret;
  }

  public function end()
  {
    return feof($this->getResource());
  }

  public function flush()
  {
    return fflush($this->getResource());
  }

  public function read($length)
  {
    return fread($this->getResource(), $length);
  }

  public function rewind()
  {
    return rewind($this->getResource());
  }

  public function seek($offset, $whence = SEEK_SET)
  {
    return fseek($this->getResource(), $offset, $whence);
  }

  public function stat()
  {
    return fstat($this->getResource());
  }

  public function tell()
  {
    return ftell($this->getResource());
  }

  public function truncate($size)
  {
    return ftruncate($this->getResource(), $size);
  }

  public function write($string, $length = null)
  {
    if( null === $length ) {
      return fwrite($this->getResource(), $string);
    } else {
      return fwrite($this->getResource(), $string, $length);
    }
  }
}