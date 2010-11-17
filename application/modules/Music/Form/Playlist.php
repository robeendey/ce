<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Playlist.php 7341 2010-09-10 03:51:24Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_Form_Playlist extends Engine_Form
{
  public $playlist;
  public $song;

  public function init()
  {
    // Init form
    $this
      ->setTitle('Add Song To Playlist')
      ->setAttrib('id',      'form-playlist-append')
      ->setAttrib('name',    'playlist_add')
      ->setAttrib('class',   '')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('format'=>'smoothbox'), 'music_playlist_append'))
      ;

    // Init playlist
    $orig_playlist = Zend_Controller_Front::getInstance()->getRequest()->getParam('playlist_id');
    foreach (Engine_Api::_()->music()->getPlaylistRows(array('user'=>Engine_Api::_()->user()->getViewer())) as $this_playlist)
      if ($this_playlist->playlist_id != $orig_playlist)
        $playlists[ $this_playlist->playlist_id ] = $this_playlist->getTitle();
      
    $playlists[0]  = Zend_Registry::get('Zend_Translate')->_('Create New Playlist');
    $this->addElement('Select', 'playlist_id', array(
      'label' => 'Choose Playlist',
      'multiOptions' => $playlists,
      'onchange' => "updateTextFields()",
    ));

    // Init new playlist field
    $this->addElement('Text', 'title', array(
      'label' => 'Playlist Name',
      'style' => '',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));

    // Init hidden file IDs
    $this->addElement('Hidden', 'song_id', array(
      'value' => Zend_Controller_Front::getInstance()->getRequest()->getParam('song_id'),
    ));
    
    // Element: execute
    $this->addElement('Button', 'execute', array(
      'label' => 'Add Song',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
      'execute',
      'cancel',
    ), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      ),
    ));
  }

  public function saveValues()
  {
    $values = $this->getValues();

    if ($values['playlist_id'] != 0)
      $playlist = $this->playlist = Engine_Api::_()->getItem('music_playlist', $values['playlist_id']);
    else {
      if (empty($values['title'])) {
        $this->getElement('title')->addErrorMessage('Required');
        return false;
      }
      $playlist = Engine_Api::_()->getDbtable('playlists', 'music')->createRow();
      $playlist->title       = trim($values['title']);
      $playlist->owner_type  = 'user';
      $playlist->owner_id    = Engine_Api::_()->user()->getViewer()->getIdentity();
      $playlist->search      = 1;
      $playlist->save();
      $playlist = $this->playlist = Engine_Api::_()->getItem('music_playlist', $playlist->playlist_id);

      // Add action and attachments
      $auth = Engine_Api::_()->authorization()->context;
      $auth->setAllowed($playlist, 'registered', 'comment', true);
      foreach( array('everyone', 'registered', 'member') as $role )
        $auth->setAllowed($playlist, $role, 'view', true);

      // Only create activity feed item if "search" is checked
      if ($playlist->search) {
        $activity = Engine_Api::_()->getDbtable('actions', 'activity');
        $action   = $activity->addActivity(
            Engine_Api::_()->user()->getViewer(),
            $playlist,
            'music_playlist_new'
        );
        $activity->attachActivity($action, $playlist);
      }

    }

    if( $playlist && $values['song_id'] > 0 )
    {
      $this->song = Engine_Api::_()->getItem('music_playlist_song', $values['song_id']);
      if ($playlist->getIdentity() && $this->song) {
        // ownership permission
        if ($playlist->owner_id != Engine_Api::_()->user()->getViewer()->getIdentity()) {
          $this->getElement('playlist_id')->addErrorMessage('This playlist does not belong to you.');
          return false;
        }
        // already exists in playlist
        foreach ($playlist->getSongs() as $song) {
          if ($song->file_id == $this->song->file_id) {
            $this->getElement('playlist_id')->addErrorMessage('This playlist already has this song.');
            return false;
          }
        }
        $playlist->addSong($this->song->file_id);
      }
    } else
      return false;
  } // end function saveValues()

}
