<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Photo.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Plugin_Signup_Photo extends Core_Plugin_FormSequence_Abstract
{
  protected $_name = 'account';

  protected $_title = 'Add Your Photo';

  protected $_formClass = 'User_Form_Signup_Photo';

  protected $_script = array('signup/form/photo.tpl', 'user');

  protected $_adminFormClass = 'User_Form_Admin_Signup_Photo';

  protected $_adminScript = array('admin-signup/photo.tpl', 'user');

  protected $_skip;

  protected $_coordinates;

  
  public function onSubmit(Zend_Controller_Request_Abstract $request)
  {
    // Form was valid
    $skip = $request->getParam("skip");
    $uploadPhoto = $request->getParam("uploadPhoto");
    $finishForm = $request->getParam("nextStep");
    $this->_coordinates = $request->getParam("coordinates");
    // do this if the form value for "skip" was not set
    // if it is set, $this->setActive(false); $this->onsubmisvalue and return true.

    if( $this->getForm()->isValid($request->getPost()) && $skip != "skipForm" && $uploadPhoto == true && $finishForm != "finish")
    {
      $this->getSession()->data = $this->getForm()->getValues();
      $this->getSession()->Filedata = $this->getForm()->Filedata->getFileInfo();
      $file = APPLICATION_PATH.'/public/temporary/'.$this->getSession()->data['Filedata'];
      $path = dirname($file);
      $name = basename($file);

      $this->_resizeImages($this->getForm()->Filedata->getFileName());

      $_SESSION['TemporaryProfileImg'] = $name;


      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }

    else if($skip != "skipForm" && $finishForm == "finish" && isset($_SESSION['TemporaryProfileImg'])){
      $this->setActive(false);
      $this->onSubmitIsValid();
      return true;
    }

    else if ($skip=="skipForm" || (!isset($_SESSION['TemporaryProfileImg'])&&$finishForm == "finish" )){
      $this->setActive(false);
      $this->onSubmitIsValid();
      $this->getSession()->skip = true;
      $this->_skip = true;
      return true;
    }
    
    // Form was not valid
    else
    {
      $this->getSession()->active = true;
      $this->onSubmitNotIsValid();
      return false;
    }







    parent::onSubmit($request);
    /*
    $photo_uploaded = !empty($this->getSession()->data['Filedata']);
    $is_uploading = !empty($_FILES['Filedata']['name']);

    // Action : reset image
    if( !empty($values['change']) )
    {
      $this->resetSession();
      return false;
    }

    // Step 1: Upload
    if( !$photo_uploaded && $is_uploading )
    {
      /// Icky hack because zend can't have a file within a subform
      $values = $this->getActionController()->getRequest()->getPost();

      // Check valid and store if yes
      if( $this->getForm()->isValid($values) )
      {
        $data = $this->getForm()->getValues();
        $this->getSession()->data = $data;
        $this->getSession()->photo_url = Zend_Controller_Front::getInstance()->getBaseUrl().'/public/temporary/p_'.$data['Filedata'];
        $this->getSession()->thumb_url = Zend_Controller_Front::getInstance()->getBaseUrl().'/public/temporary/is_'.$data['Filedata'];
        $this->_resizeImages($this->getForm()->getFileElement()->getFileName());
        return true;
      }
      else
      {
        return false;
      }
    }

    // Step 2: Accept
    else if( $photo_uploaded )
    {
      $this->getSession()->active = false;
    }
     * 
     */
  }
  
  public function onProcess()
  {
    $data = $this->getSession()->data;

    $viewer = Engine_Api::_()->user()->getViewer();
    unset($_SESSION['TemporaryProfileImg']);
    $params = array(
      'parent_type' => 'viewer',
      'parent_id' => $viewer->user_id
    );

    if( !$this->_skip && !$this->getSession()->skip ) {
      // Save
      $storage = Engine_Api::_()->storage();
      $file = APPLICATION_PATH.'/public/temporary/'.$this->getSession()->data['Filedata'];
      $name = basename($file);
      $path = dirname($file);

      // Store
      $iMain = $storage->create($path.'/m_'.$name, $params);
      $iProfile = $storage->create($path.'/p_'.$name, $params);
      $iIconNormal = $storage->create($path.'/in_'.$name, $params);
      $iSquare = $storage->create($path.'/is_'.$name, $params);

      $iMain->bridge($iProfile, 'thumb.profile');
      $iMain->bridge($iIconNormal, 'thumb.normal');
      $iMain->bridge($iSquare, 'thumb.icon');

      // Remove temp files
      @unlink($path.'/p_'.$name);
      @unlink($path.'/m_'.$name);
      @unlink($path.'/in_'.$name);
      @unlink($path.'/is_'.$name);

      // Update row
      $viewer->photo_id = $iMain->file_id;
      $viewer->save();

      if ($this->_coordinates){
        $this->_resizeThumbnail($viewer);
      }
    }
  }

  protected function _resizeImages($file)
  {
    $name = basename($file);
    $path = dirname($file);

    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(720, 720)
      ->write($path.'/m_'.$name)
      ->destroy();

    // Resize image (profile)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(200, 400)
      ->write($path.'/p_'.$name)
      ->destroy();

    // Resize image (icon.normal)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(48, 120)
      ->write($path.'/in_'.$name)
      ->destroy();

    // Resize image (icon.square)
    $image = Engine_Image::factory();
    $image->open($file);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->resample($x, $y, $size, $size, 48, 48)
      ->write($path.'/is_'.$name)
      ->destroy();
   }
   
   protected function _resizeThumbnail($user)
   {
      $storage = Engine_Api::_()->storage();

      $iProfile = $storage->get($user->photo_id, 'thumb.profile');
      $iSquare = $storage->get($user->photo_id, 'thumb.icon');

      // Read into tmp file
      $pName = $iProfile->getStorageService()->temporary($iProfile);
      $iName = dirname($pName) . '/nis_' . basename($pName);

      list($x, $y, $w, $h) = explode(':', $this->_coordinates);

      $image = Engine_Image::factory();
      $image->open($pName)
        ->resample($x+.1, $y+.1, $w-.1, $h-.1, 48, 48)
        ->write($iName)
        ->destroy();

      $iSquare->store($iName);

      @unlink($iName);
   }

  public function onAdminProcess($form)
  {
    $step_table = Engine_Api::_()->getDbtable('signup', 'user');
    $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Photo'));
    $step_row->enable = $form->getValue('enable');
    $step_row->save();
  }
}