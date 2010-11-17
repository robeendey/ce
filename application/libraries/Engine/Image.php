<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Image
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Image.php 7244 2010-09-01 01:49:53Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Image
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
abstract class Engine_Image
{
  /**
   * The image resource
   * 
   * @var resource
   */
  protected $_resource;

  /**
   * Contains information about the current image
   * 
   * @var stdClass
   */
  protected $_info;

  /**
   * Factory method. Automatically picks available adapter
   * 
   * @param string $adapter Force this adapter
   * @param array $options
   * @return Engine_Image
   */
  public function factory($adapter = 'gd', array $options = array())
  {
    $class = 'Engine_Image_Adapter_'.ucfirst($adapter);
    return new $class($options);
  }

  /**
   * Destructor
   */
  public function __destruct()
  {
    $this->destroy();
  }

  /**
   * Magic getter for image info
   *
   * @param string $key
   * @return mixed
   */
  public function __get($key)
  {
    if( isset($this->_info->$key) )
    {
      return $this->_info->$key;
    }

    return null;
  }

  /**
   * Get the image resource
   * 
   * @return resource
   */
  public function getResource()
  {
    return $this->_resource;
  }

  abstract public function open($file);

  abstract public function destroy();

  abstract public function write($file = null, $format = 'jpg');

  abstract public function output();

  abstract public function resize($w, $h, $aspect = true);

  abstract public function crop($x, $y, $w, $h);
  
  abstract public function resample($srcX, $srcY, $srcW, $srcH, $dstW, $dstH);
}