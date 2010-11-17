<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Image
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Gd.php 7468 2010-09-25 01:01:58Z john $
 * @todo       documentation
 */

/**
 * @category   Engine
 * @package    Engine_Image
 * @copyright  Copyright 2006-2010 Webligo Developments 
 * @license    http://www.socialengine.net/license/
 */
class Engine_Image_Adapter_Gd extends Engine_Image
{
  /**
   * Image format support
   * 
   * @var array
   */
  protected static $_support;

  /**
   * Quality (0-100)
   * 
   * @var integer
   */
  protected $_quality;

  /**
   * Constructor
   * 
   * @param string $file Image to open
   */
   /*
  public function __construct($file = null)
  {
    if( null !== $file )
    {
      $this->open($file);
    }
  }
  */

  /**
   * Open an image
   * 
   * @param string $file
   * @return Engine_Image_Adapter_Gd
   * @throws Engine_Image_Adapter_Exception If unable to open
   */
  public function open($file)
  {
    // Get image info
    $info = @getimagesize($file);
    if( !$info ) {
      throw new Engine_Image_Adapter_Exception(sprintf("File \"%s\" is not an image or does not exist", $file));
    }

    // Check if we can open the file
    self::_isSafeToOpen($info[0], $info[1]);

    // Detect type
    $type = ltrim(strrchr('.', $file), '.');
    if( !$type )
    {
      $type = image_type_to_extension($info[2], false);
    }
    $type = strtolower($type);

    // Check support
    self::_isSupported($type);
    $function = 'imagecreatefrom'.$type;
    if( !function_exists($function) )
    {
      throw new Engine_Image_Adapter_Exception(sprintf('Image type "%s" is not supported', $type));
    }

    // Open
    $this->_resource = $function($file);
    if( !$this->_resource )
    {
      throw new Engine_Image_Adapter_Exception("Unable to open image");
    }

    $this->_info = new stdClass();
    $this->_info->type = $type;
    $this->_info->file = $file;
    $this->_info->width = $info[0];
    $this->_info->height = $info[1];
    
    return $this;
  }

  /**
   * Write current image to a file
   * 
   * @param string $file (OPTIONAL) The file to write to. Default: original file
   * @param string $type (OPTIONAL) The output image type. Default: jpeg
   * @return Engine_Image_Adapter_Gd
   * @throws Engine_Image_Adapter_Exception If unable to write
   */
  public function write($file = null, $type = 'jpeg')
  {
    // If no file specified, write to existing file
    if( null === $file )
    {
      $file = $this->file;
      if( !$file )
      {
        throw new Engine_Image_Adapter_Exception("No file to write specified.");
      }
    }

    // Check support
    self::_isSupported($type);
    $function = 'image'.$type;
    if( !function_exists($function) ) {
      throw new Engine_Image_Adapter_Exception(sprintf('Image type "%s" is not supported', $type));
    }

    $quality = null;
    if( is_int($this->_quality) && $this->_quality >= 0 && $this->_quality <= 100 ) {
      $quality = $this->_quality;
    }
    
    if( $function == 'imagejpeg' && null !== $quality ) {
      $result = $function($this->_resource, $file, $quality);
    } else if( $function == 'imagepng' && null !== $quality ) {
      $result = $function($this->_resource, $file, round(abs(($quality - 100) / 11.111111)));
    } else {
      $result = $function($this->_resource, $file);
    }
    
    if( !$result ) {
      throw new Engine_Image_Adapter_Exception(sprintf("Unable to write image to file %s", $file));
    }

    return $this;
  }

  /**
   * Remove the current image object from memory
   */
  public function destroy()
  {
    $this->_info = new stdClass();
    if( is_resource($this->_resource) )
    {
      imagedestroy($this->_resource);
    }
  }

  /**
   * Output an image to buffer or return as string
   * 
   * @param string $type Image format
   * @param boolean $buffer Output or return?
   * @return mixed
   * @throws Engine_Image_Adapter_Exception If unable to output
   */
  public function output($type = 'jpeg', $buffer = false)
  {
    // Check support
    self::_isSupported($type);
    $function = 'image'.$type;
    if( !function_exists($function) )
    {
      throw new Engine_Image_Adapter_Exception("Image type \'{$type}\' is not supported");
    }

    if( $buffer ) ob_start();
    if( !$function($this->_resource, $file) )
    {
      if( $buffer ) ob_end_clean();
      throw new Engine_Image_Adapter_Exception("Unable to output image");
    }

    if( $buffer )
    {
      return ob_get_clean();
    }

    return $this;
  }

  /**
   * Resizes current image to $width and $height. If aspect is set, will fit
   * within boundaries while keeping aspect
   * 
   * @param integer $width
   * @param integer $height
   * @param boolean $aspect (OPTIONAL) Default - true
   * @return Engine_Image_Adapter_Gd
   * @throws Engine_Image_Adapter_Exception If unable to resize
   */
  public function resize($width, $height, $aspect = true)
  {
    $imgW = $this->width;
    $imgH = $this->height;

    // Keep aspect
    if( $aspect )
    {
      list($width, $height) = self::_fitImage($imgW, $imgH, $width, $height);
    }

    // Create new temporary image
    self::_isSafeToOpen($width, $height);
    $dst = imagecreatetruecolor($width, $height);

    // Try to preserve transparency
    //self::_allocateTransparency($this->_resource, $dst, $this->_info->type);

    // Resize
    if( !imagecopyresampled($dst, $this->_resource, 0, 0, 0, 0, $width, $height, $imgW, $imgH) )
    {
      imagedestroy($dst);
      throw new Engine_Image_Adapter_Exception('Unable to resize image');
    }

    // Now destroy old image and overwrite with new
    imagedestroy($this->_resource);
    $this->_resource = $dst;
    $this->_info->width = $width;
    $this->_info->height = $height;
    
    return $this;
  }

  /**
   * Crop an image
   * 
   * @param integer $x
   * @param integer $y
   * @param integer $w
   * @param integer $h
   * @return Engine_Image_Adapter_Gd
   * @throws Engine_Image_Adapter_Exception If unable to crop
   */
  public function crop($x, $y, $w, $h)
  {
    // Create new temporary image and resize
    self::_isSafeToOpen($w, $h);
    $dst = imagecreatetruecolor($w, $h);
    if( !imagecopy($dst, $this->_resource, 0, 0, $x, $y, $w, $h) )
    {
      imagedestroy($dst);
      throw new Engine_Image_Adapter_Exception('Unable to crop image');
    }

    // Now destroy old image and overwrite with new
    imagedestroy($this->_resource);
    $this->_resource = $dst;
    $this->_info->width = $w;
    $this->_info->height = $h;
    
    return $this;
  }

  /**
   * Resample. Similar to resize, but allows you to specify a source area and
   * target size
   * 
   * @param integer $srcX
   * @param integer $srcY
   * @param integer $srcW
   * @param integer $srcH
   * @param integer $dstW
   * @param integer $dstH
   * @return Engine_Image_Adapter_Gd
   * @throws Engine_Image_Adapter_Exception If unable to crop
   */
  public function resample($srcX, $srcY, $srcW, $srcH, $dstW, $dstH)
  {
    // Create new temporary image
    self::_isSafeToOpen($dstW, $dstH);
    $dst = imagecreatetruecolor($dstW, $dstH);

    // Try to preserve transparency
    //self::_allocateTransparency($this->_resource, $dst, $this->_info->type);

    // Resample
    if( !imagecopyresampled($dst, $this->_resource, 0, 0, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH) )
    {
      imagedestroy($dst);
      throw new Engine_Image_Adapter_Exception('Unable to resample image');
    }

    // Now destroy old image and overwrite with new
    imagedestroy($this->_resource);
    $this->_resource = $dst;
    $this->_info->width = $dstW;
    $this->_info->height = $dstH;
    
    return $this;
  }



  // Static

  /**
   * Check if it is safe to open an image (memory-wise)
   * 
   * @param integer $width Width in pixels
   * @param integer $height Height in pixels
   * @param integer $bpp Bytes per pixel
   */
  protected static function _isSafeToOpen($width, $height, $bpp = 4)
  {
    // "Fudge Factor"
    $fudge = 1.2;
    
    if( !function_exists('memory_get_usage') )
    {
      $used = 5 * 1024 * 1024;
    }
    else
    {
      $used = memory_get_usage();
    }
    
    $limit = ini_get('memory_limit');
    if( !$limit )
    {
      $limit = 8 * 1024 * 1024;
    }

    else
    {
      $limit = self::_convertBytes($limit);
    }

    $required = $width * $height * $bpp * $fudge;

    if( $limit - $used < $required )
    {
      throw new Engine_Image_Exception("Insufficient memory to open image ({$limit} - {$used} < {$required})");
    }
  }

  /**
   * Get supported format info
   * 
   * @return stdClass
   */
  protected static function getSupport()
  {
    if( null === self::$_support )
    {
      $info = ( function_exists('gd_info') ? gd_info() : array() );
      $support = new stdClass();
      
      $support->freetype = !empty($info["FreeType Support"]);
      $support->t1lib = !empty($info["T1Lib Support"]);
      $support->gif = ( !empty($info["GIF Read Support"]) && !empty($info["GIF Create Support"]) );
      $support->jpg = ( !empty($info["JPG Support"]) || !empty($info["JPEG Support"]) );
      $support->jpeg = $support->jpg;
      $support->png = !empty($info["PNG Support"]);
      $support->wbmp = !empty($info["WBMP Support"]);
      $support->xbm = !empty($info["XBM Support"]);

      self::$_support = $support;
    }

    return self::$_support;
  }

  /**
   * Check if a specific image type is supported
   * 
   * @param string $type
   * @param boolean $throw
   * @return boolean
   * @throws Engine_Image_Adapter_Exception If $throw is true and not supported
   */
  protected static function _isSupported($type, $throw = true)
  {
    if( empty(self::getSupport()->$type) )
    {
      if( $throw )
      {
        throw new Engine_Image_Adapter_Exception(sprintf('Image type %s is not supported', $type));
      }
      return false;
    }
    return true;
  }

  /**
   * Convert short-hand bytes to integer
   * 
   * @param string $value
   * @return integer
   */
  protected static function _convertBytes($value)
  {
    if( is_numeric( $value ) )
    {
      return $value;
    }
    else
    {
      $value_length = strlen( $value );
      $qty = substr( $value, 0, $value_length - 1 );
      $unit = strtolower( substr( $value, $value_length - 1 ) );
      switch ( $unit )
      {
        case 'k':
          $qty *= 1024;
          break;
        case 'm':
          $qty *= 1048576;
          break;
        case 'g':
          $qty *= 1073741824;
          break;
      }
      return $qty;
    }
  }

  /**
   * Fits a square within another square!
   * 
   * @param integer $dstW
   * @param integer $dstH
   * @param integer $maxW
   * @param integer $maxH
   * @param unknown $method No idea what this was for
   * @return array
   */
  protected static function _fitImage($dstW, $dstH, $maxW, $maxH, $method = null)
  {
    if( ($delta = $maxW / $dstW) < 1 )
    {
      $dstH = floor($dstH * $delta);
      $dstW = floor($dstW * $delta);
    }
    if( ($delta = $maxH / $dstH) < 1 )
    {
      $dstH = floor($dstH * $delta);
      $dstW = floor($dstW * $delta);
    }
    return array($dstW, $dstH);
  }

  protected static function _allocateTransparency(&$img1, &$img2, $type)
  {
    if( $type == 'gif' || $type == 'png' ) {
      //if( $type == 'gif' ) {
        $transparent_index = imagecolortransparent($img1);
        // GIF
        if( $transparent_index >= 0 ) {
          $transparent_color = imagecolorsforindex($img1, $transparent_index);
          $transparent_index2 = imagecolorallocate($img2, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
          imagefill($img2, 0, 0, $transparent_index2);
          imagecolortransparent($img2, $transparent_index2);
        }
      //}
      // PNG
      else if( $type == 'png' )
      {
        imagealphablending($img2, false);
        $transparent_color = imagecolorallocatealpha($img2, 0, 0, 0, 127);
        imagefill($img2, 0, 0, $transparent_color);
        imagesavealpha($img2, true);
      }
    }
  }
}


if( !function_exists('image_type_to_extension') ) {

  function image_type_to_extension($type, $dot = true) {
    $e = array(1 => 'gif', 'jpeg', 'png', 'swf', 'psd', 'bmp',
      'tiff', 'tiff', 'jpc', 'jp2', 'jpf', 'jb2', 'swc',
      'aiff', 'wbmp', 'xbm');

    // We are expecting an integer.
    $type = (int)$type;
    if( !$type ) {
      trigger_error( 'type must be an integer', E_USER_NOTICE );
      return null;
    }

    if( !isset($e[$type]) ) {
      trigger_error( 'No corresponding image type', E_USER_NOTICE );
      return null;
    }

    return ($dot ? '.' : '') . $e[$type];
  }

}

if( !function_exists('image_type_to_mime_type') ) {

  function image_type_to_mime_type($type) {
    $m = array(1 => 'image/gif', 'image/jpeg', 'image/png',
      'application/x-shockwave-flash', 'image/psd', 'image/bmp',
      'image/tiff', 'image/tiff', 'application/octet-stream','image/jp2',
      'application/octet-stream', 'application/octet-stream',
      'application/x-shockwave-flash', 'image/iff', 'image/vnd.wap.wbmp',
      'image/xbm');

    // We are expecting an integer.
    $type = (int)$type;
    if( !$type ) {
      trigger_error( 'type must be an integer', E_USER_NOTICE );
      return null;
    }

    if( !isset($m[$type]) ) {
      trigger_error( 'No corresponding image type', E_USER_NOTICE );
      return null;
    }

    return $m[$type];
  }

}

if( !function_exists('imagecreatefrombmp') )
{
  function imagecreatefrombmp($filename)
  {
    
  }
}
