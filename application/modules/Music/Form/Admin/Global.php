<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Global.php 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');

    $this->addElement('Radio', 'music_public', array(
      'label' => 'Public Permissions',
      'description' => 'MUSIC_FORM_ADMIN_GLOBAL_MUSICPUBLIC_DESCRIPTION',
      'multiOptions' => array(
        1 => 'Yes, the public can view playlists unless they are made private.',
        0 => 'No, the public cannot view playlists.'
      ),
      'value' => 1,
    ));

    $this->addElement('Text', 'playlistsPerPage', array(
      'label' => 'Playlists Per Page',
      'description' => 'How many playlists will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsPerPage', 10),
    ));


    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }

  public function saveValues()
  {
    $values = $this->getValues();
    if (!is_numeric($values['playlistsPerPage'])
           || 0  >= $values['playlistsPerPage']
           || 999 < $values['playlistsPerPage'])
      $values['playlistsPerPage'] = 10;
    Engine_Api::_()->getApi('settings', 'core')->setSetting('music.playlistsPerPage', $values['playlistsPerPage']);

    $auth = Engine_Api::_()->getApi('core', 'authorization')->getAdapter('levels');
    $auth->setAllowed('music_playlist', Engine_Api::_()->getDbtable('levels', 'authorization')->getPublicLevel()->level_id, 'view', $values['music_public']);
  }
}