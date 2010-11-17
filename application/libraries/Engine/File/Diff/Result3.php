<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_File_Diff
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Result3.php 7244 2010-09-01 01:49:53Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Engine
 * @package    Engine_File_Diff
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @author     John Boehr <j@webligo.com>
 */
class Engine_File_Diff_Result3 extends Engine_File_Diff_Result
{
  protected $_original;
  
  public function __construct($status, $left, $right, $original)
  {
    if( !is_numeric($status) ) {
      throw new Engine_File_Diff_Exception(sprintf('Invalid status type given to "%1$s::%2$s": %3$s', get_class($this), __METHOD__, gettype($status)));
    }
    $this->_status = $status;
    $this->_left = $this->_procFile($left);
    $this->_right = $this->_procFile($right);
    $this->_original = $this->_procFile($original);
  }

  public function getOriginal()
  {
    return $this->_original;
  }
}