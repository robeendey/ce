<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Adphoto.php 7418 2010-09-20 00:18:02Z john $
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_Adphoto extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getPhotoUrl($type = null)
  {
    if( empty($this->file_id) )
    {
      return "no file id";
    }

    $file = $this->api()->getApi('storage', 'storage')->get($this->file_id, $type);
    if( !$file )
    {
      return "no file";
    }

    return $file->map();
  }
}