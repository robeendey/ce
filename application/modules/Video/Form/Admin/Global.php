<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Video_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'video_ffmpeg_path', array(
      'label' => 'Path to FFMPEG',
      'description' => 'Please enter the full path to your FFMPEG installation. (Environment variables are not present)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.ffmpeg.path', ''),
    ));
    
    $this->addElement('Text', 'video_jobs', array(
      'label' => 'Encoding Jobs',
      'description' => 'How many jobs do you want to allow to run at the same time?',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.jobs', 2),
    ));

    $this->addElement('Radio', 'video_permission', array(
      'label' => 'Public Permissions',
      'description' => 'Select whether or not you want to let the public (visitors that are not logged-in) to view the following sections of your social network. In some cases (such as Profiles, Blogs, and Albums), if you have given them the option, your users will be able to make their pages private even though you have made them publically viewable here. For more permissions settings, please visit the General Settings page.',
      'multiOptions' => array(
        1 => 'Yes, the public can view videos unless they are made private.',
        0 => 'No, the public cannot view videos.'
      ),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.permission', 1),
    ));

    $this->addElement('Text', 'video_page', array(
      'label' => 'Listings Per Page',
      'description' => 'How many videos will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('video.page', 10),
    ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}