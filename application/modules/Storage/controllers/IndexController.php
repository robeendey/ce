<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Storage_IndexController extends Core_Controller_Action_Standard
{
  public function serveAction()
  {
    $file_id = $this->_getParam('file');
    $file = Engine_Api::_()->getItem('storage_file', $file_id);

    if( $file && ($file instanceof Storage_Model_File) && $file->getIdentity() )
    {
      Engine_Api::_()->core()->setSubject($file);
    }
    if( !$this->_helper->requireSubject('storage_file')->isValid() ) return;

    // Set body and headers
    $mime = $file->mime_major . '/' . $file->mime_minor;
    $this->getResponse()->setHeader('Content-Type', $mime, true);
    $this->getResponse()->setBody($file->read());

    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
  }
}