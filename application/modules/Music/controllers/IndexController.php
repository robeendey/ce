<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: IndexController.php 7592 2010-10-06 23:13:03Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
// @todo add in authorization settings for upload sizes/quantity
class Music_IndexController extends Core_Controller_Action_Standard
{
  protected $_paginate_params = array();

  public function init()
  {
    $this->view->viewer_id  = Engine_Api::_()->user()->getViewer()->getIdentity();
    $this->view->navigation = $this->getNavigation();
    $this->_paginate_params['limit']  = Engine_Api::_()->getApi('settings', 'core')->getSetting('music.playlistsPerPage', 10);
    $this->_paginate_params['sort']   = $this->getRequest()->getParam('sort', 'recent');
    $this->_paginate_params['page']   = $this->getRequest()->getParam('page', 1);
    $this->_paginate_params['search'] = $this->getRequest()->getParam('search', '');
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'view')->isValid()) return;
  }
  
  public function browseAction()
  {
    $this->view->search_form = $search_form = new Music_Form_Search();
    if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page' => 1,
        'sort'   => $this->getRequest()->getPost('sort'),
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $search_form->getElement('search')->setValue($this->_getParam('search'));
      $search_form->getElement('sort')->setValue($this->_getParam('sort'));
    }
    
    $params = array_merge($this->_paginate_params, array());
    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if ($user_id) $params = array_merge($this->_paginate_params, array('user' => $user_id));

    $this->view->paginator = Engine_Api::_()->music()->getPlaylistPaginator($params);
  }
  
  public function manageAction()
  {
    $this->view->search_form = $search_form = new Music_Form_Search();
    if ($this->getRequest()->isPost() && $search_form->isValid($this->getRequest()->getPost())) {
      $this->_helper->redirector->gotoRouteAndExit(array(
        'page' => 1,
        'sort'   => $this->getRequest()->getPost('sort'),
        'search' => $this->getRequest()->getPost('search'),
      ));
    } else {
      $search_form->getElement('search')->setValue($this->_getParam('search'));
      $search_form->getElement('sort')->setValue($this->_getParam('sort'));
    }
    // only members can manage music
    if( !$this->_helper->requireUser()->isValid() ) return;

    $params = array_merge($this->_paginate_params, array(
        'user' => $this->view->viewer_id,
    ));
    $this->view->paginator = Engine_Api::_()->music()->getPlaylistPaginator($params);
  }

  public function createAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid() ) {
      return;
    }

    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('upload-song', null, null, array('format' => 'json'));
    }

    $this->view->form = $form = new Music_Form_Create();
    $this->view->playlist_id = $this->_getParam('playlist_id', '0');

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }


    // Process
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist = $this->view->form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }
    
    return $this->_helper->redirector->gotoUrl($playlist->getHref(), array('prependBase' => false));
  }

  public function editAction()
  {
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }

    // catch uploads from FLASH fancy-uploader and redirect to uploadSongAction()
    if( $this->getRequest()->getQuery('ul', false) ) {
      return $this->_forward('edit-add-song', null, null, array('format' => 'json'));
    }

    // Get playlist
    $playlist_id = $this->_getParam('playlist_id');
    if( !$playlist_id ) {
      $this->_helper->redirector->gotoUrl(array(), 'music_browse', true);
    }
    
    $this->view->playlist = $playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id);
    if( !$playlist ) {
      $this->_helper->redirector->gotoUrl(array(), 'music_browse', true);
      return;
    }

    // only user and admins and moderators can create
    if( !$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid() ) {
      return;
    }
     
    foreach( $this->_navigation->getPages() as $page ) {
      if( $page->route == 'music_manage' ) {
        $page->setActive(true);
      }
    }

    // Make form
    $this->view->form = $form = new Music_Form_Edit();
    $form->populate($playlist);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $form->saveValues();
      $db->commit();
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    return $this->_helper->redirector->gotoUrl($playlist->getHref(), array('prependBase' => false));
  }

  public function editAddSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->_helper->requireUser()->isValid()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('You must be logged in.');
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('You are not allowed to upload songs.');
      return;
    }

    $playlist_id = $this->getRequest()->getParam('playlist_id', false);
    if (false === $playlist_id) {
      $this->view->dump = $this->getRequest();
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid playlist');
      return;
    }

    // if the song was uploaded via composer, playlist_id == -1
    // so we'll need to fetch the true playlist_id, or create it
    $type = $this->_getParam('type', 'wall');
    if ($playlist_id == -1 && $type == 'wall') {
      $pl_table = Engine_Api::_()->getDbTable('playlists', 'music');
      $select   = Engine_Api::_()->music()->getPlaylistSelect(array('user'=>$this->view->viewer_id))
                  ->where('composer = 1')
                  ->limit(1);
      $row = $pl_table->fetchRow($select);
      if (!empty($row))
        $playlist_id = $row->playlist_id;
      else {
        $db = $pl_table->getAdapter();
        $db->beginTransaction();
        try {
          $row = $pl_table->createRow();
          $row->title = $translate->_('_MUSIC_DEFAULT_PLAYLIST');
          $row->owner_type = 'user';
          $row->owner_id   = $this->view->viewer_id;
          $row->composer   = 1;
          $row->search     = 1;
          $row->save();
          $playlist_id = $row->playlist_id;

          // Authorizations
          $auth      = Engine_Api::_()->authorization()->context;
          $auth->setAllowed($row, 'everyone', 'view',    true);
          $auth->setAllowed($row, 'everyone', 'comment', true);

          $db->commit();
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
          $this->view->error   = $translate->_('Unable to create default playlist in database');
          return;
        }
      }
    }
    else if ($playlist_id == -1 && $type == 'message'){
      $pl_table = Engine_Api::_()->getDbTable('playlists', 'music');
      $db = $pl_table->getAdapter();
      $db->beginTransaction();
      try {
        $row = $pl_table->createRow();
        $row->title = $translate->_('_MUSIC_MESSAGE_PLAYLIST');
        $row->owner_type = 'user';
        $row->owner_id   = $this->view->viewer_id;
        // composer 2 == it's a message
        $row->composer   = 2;
        $row->search     = 0;
        $row->save();
        $playlist_id = $row->playlist_id;

        // Authorizations
        $auth      = Engine_Api::_()->authorization()->context;
        $auth->setAllowed($row, 'owner', 'view',    true);
        $auth->setAllowed($row, 'owner', 'comment', true);

        $db->commit();
      } catch (Exception $e) {
        $db->rollback();
        $this->view->success = false;
        $this->view->error   = $translate->_('Unable to create default playlist in database');
        return;
      }
    }
    
    // only owner and moderators can edit this playlist
    $playlist = Engine_Api::_()->getItem('music_playlist', $playlist_id);
    if (!$playlist->isEditable()) {
      $this->view->success = false;
      $this->view->error   = Zend_Registry::get('Zend_Translate')->_('You are not allowed to edit this playlist');
      return;
    }

    // this is already being done in a transaction:
    $this->uploadSongAction();

    // we want to do the assigning-to-playlist in a transaction, though
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist->addSong($this->view->song);
      $song     = $playlist->getSong($this->view->song->file_id);
      if ($song) {
        $db->commit();
        $this->view->success     = true;
        $this->view->song_id     = $song->song_id;
        $this->view->song_url    = $song->getFilePath();
        $this->view->song_title  = $song->getTitle();
      } else {
        $db->rollback();
        $this->view->success     = false;
        $this->view->error       = Zend_Registry::get('Zend_Translate')->_('Song was not successfully attached');
      }
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success   = false;
      $this->view->error     = Zend_Registry::get('Zend_Translate')->_('Unable to add song to playlist');
      $this->view->exception = $e->__toString();

    }
  }

  public function deleteAction()
  {
    $playlist = Engine_Api::_()->getItem('music_playlist', $this->getRequest()->getParam('playlist_id'));

    if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'delete')->isValid())
      return;
    
    $this->view->playlist_id = $playlist->getIdentity();
    
    if (!$this->getRequest()->isPost())
      return;
    $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      foreach ($playlist->getSongs() as $song)
        $song->deleteUnused();
      $playlist->delete();
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      throw $e;
    }
  }
  public function playlistAction()
  {
    // if this is sending a message id, the user is being directed from a coversation
    // check if member is part of the conversation
    $message_id = $this->getRequest()->getParam('message');
    $message_view = false;
    if ($message_id){
      $conversation = Engine_Api::_()->getItem('messages_conversation', $message_id);
      if($conversation->hasRecipient(Engine_Api::_()->user()->getViewer())) $message_view = true;
    }
    $this->view->message_view = $message_view;
    $this->view->playlist = Engine_Api::_()->getItem('music_playlist', $this->getRequest()->getParam('playlist_id'));
    if (!empty($this->view->playlist)) {
      Engine_Api::_()->core()->setSubject($this->view->playlist);
    }
    if (!$this->_helper->requireSubject()->isValid())
      return;

    if (!$message_view && !$this->_helper->requireAuth()->setAuthParams($this->view->playlist, null, 'view')->isValid())
      return;

    if ($this->_getParam('popout')) {
      $this->view->popout = true;
      $this->_helper->layout->setLayout('default-simple');
    }
  }
  public function playlistSortAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $playlist  = Engine_Api::_()->getItem('music_playlist', $this->getRequest()->getParam('playlist_id'));
    if (!$this->getRequest()->isPost() || !$playlist || $this->view->viewer_id !== $playlist->owner_id) {
      $this->view->error = $translate->_('Invalid playlist');
      return;
    }

    if (!$playlist->isEditable()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not allowed to edit this playlsit');
      return;
    }
    
    $songs = $playlist->getSongs();
    $order = explode(',', $this->getRequest()->getParam('order'));
    foreach ($order as $i => $item) {
      $song_id = substr($item, strrpos($item, '_')+1);
      foreach ($songs as $song) {
        if ($song->song_id == $song_id) {
            $song->order = $i;
            $song->save();
        }
      }
    }
    $this->view->songs    = $playlist->getSongs()->toArray();
  }
  public function renameSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid request method');
      exit;
    }

    $song     = Engine_Api::_()->getItem('music_playlist_song', $this->getRequest()->getParam('song_id'));
    if (!$song) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not a valid song');
      return;
    }

    $playlist = $this->view->playlist = $song->getParent();
    if (!$playlist) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid playlist');
      return;
    }

    if (!$playlist->isEditable()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not allowed to edit this playlist');
      return;
    }

    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
        $song->setTitle( $this->getRequest()->getParam('title') );
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('Unknown database error');
      throw $e;
    }
  }
  public function removeSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    if (!$this->getRequest()->isPost()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('isGet');
      exit;
    }

    $song     = Engine_Api::_()->getItem('music_playlist_song', $this->getRequest()->getParam('song_id'));
    if (!$song) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Not a valid song');
      $this->view->post    = $_POST;
      return;
    }

    $playlist = Engine_Api::_()->getItem('music_playlist', $this->_getParam('playlist_id'));
    if (!$playlist || !$playlist->isEditable()) {
      $this->view->success = false;
      $this->view->error   = $translate->_('You are not allowed to edit this playlist');
      return;
    }
    
    if (null === $playlist->getSong($song->file_id)) {
      $this->view->success = false;
      $this->view->error   = $translate->_('Invalid playlist');
      return;
    }

    
    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $song->deleteUnused();
      $db->commit();
      $this->view->success = true;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
      $this->view->error   = $translate->_('Unknown database error');
      throw $e;
    }
  }
  public function playlistAppendAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    // only members can upload music
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->_helper->requireAuth()->setAuthParams('music_playlist', null, 'create')->isValid())
      return;

    $this->view->form        = new Music_Form_Playlist();
    $this->view->playlist_id = $this->getRequest()->getParam('playlist_id');
    $this->view->song_id     = $this->getRequest()->getParam('song_id');
    
    if ( $this->getRequest()->isPost() && $this->view->form->isValid($this->getRequest()->getPost()) ) {
      $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
      $db->beginTransaction();
      try {
        $this->view->form->saveValues();
        $db->commit();
        $this->view->success     = true;
        $this->view->playlist_id = $this->view->form->playlist->playlist_id;
        $this->view->message = $translate->_('Your changes have been saved.');
      } catch (Exception $e) {
        $db->rollback();
        $this->view->success = false;
      }
    }
  }
  public function songPlayTallyAction()
  {
    $song     = Engine_Api::_()->getItem('music_playlist_song', $this->getRequest()->getParam('song_id'));
    $playlist = $song ? $song->getParent() : false;
    if ( $this->getRequest()->isPost() && $playlist ) {
        $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
        $db->beginTransaction();
        try {
          $song->play_count++;
          $song->save();
          $playlist->play_count++;
          $playlist->save();

          $db->commit();

          $this->view->success = true;
          $this->view->song = $song->toArray();
          $this->view->play_count = $song->playCountLanguagified();
        } catch (Exception $e) {
          $db->rollback();
          $this->view->success = false;
        }
      
    } else {
      $this->view->success = false;
      $this->view->error   = Zend_Registry::get('Zend_Translate')->_('invalid song_id');
    }
  }
  public function setProfilePlaylistAction()
  {
    if (! $this->getRequest()->isPost() )
      return;

    $playlist = Engine_Api::_()->getItem('music_playlist', $this->getRequest()->getPost('playlist_id', null));
    if (!$playlist || $playlist->owner_id != $this->view->viewer_id)
      return;

    $this->view->playlist_id = $playlist->getIdentity();

    $db = Engine_Api::_()->getDbTable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $playlist->setProfile();
      $db->commit();
      $this->view->success = true;
      $this->view->enabled = $playlist->profile;
    } catch (Exception $e) {
      $db->rollback();
      $this->view->success = false;
    }
  }  
  public function uploadSongAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    // only members can upload music
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error  = $translate->_('Max file size limit exceeded or session expired.');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error  = $translate->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error  = $translate->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error  = $translate->_('Invalid Upload or file too large');
      return;
    }

    if( !preg_match('/\.(mp3|m4a|aac|mp4)$/', $_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error  = $translate->_('Invalid file type');
    }

    $db = Engine_Api::_()->getDbtable('playlists', 'music')->getAdapter();
    $db->beginTransaction();
    try {
      $song = $this->view->song = Engine_Api::_()->getApi('core', 'music')->createSong($_FILES['Filedata']);
      $this->view->status   = true;
      $this->view->song_id  = $song->getIdentity();
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      $this->view->status  = false;
      $this->view->message = $translate->_('Upload failed by database query');
      throw $e;
    }
    
  }


  /* Utility */
  protected $_navigation;
  public function getNavigation()
  {
    $tabs   = array();
    $tabs[] = array(
          'label'      => 'Browse Music',
          'route'      => 'music_browse',
          'action'     => 'browse',
          'controller' => 'index',
          'module'     => 'music'
        );
    $tabs[] = array(
          'label'      => 'My Music',
          'route'      => 'music_manage',
          'action'     => 'manage',
          'controller' => 'index',
          'module'     => 'music'
        );
    $tabs[] = array(
          'label'      => 'Upload Music',
          'route'      => 'music_create',
          'action'     => 'create',
          'controller' => 'index',
          'module'     => 'music'
        );
    if( is_null($this->_navigation) ) {
      $this->_navigation = new Zend_Navigation();
      $this->_navigation->addPages($tabs);
    }
    return $this->_navigation;
  }
}