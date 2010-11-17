<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminManageController.php 7518 2010-10-01 09:27:40Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_AdminManageController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('music_admin_main', array(), 'music_admin_main_manage');
  }
  
  public function indexAction()
  {
    $select    = Engine_Api::_()->getApi('core', 'music')->getPlaylistSelect();
    $select->orWhere('1=1'); // display all playlists
    $paginator = $this->view->paginator = Zend_Paginator::factory($select);
  }

  public function suggestAction()
  {
    $page = $this->_getParam('page');
    $query = $this->_getParam('query');

    $playlistTable = Engine_Api::_()->getItemTable('music_playlist');
    $playlistSelect = $playlistTable->select()
      ->where('title LIKE ?', '%' . $query . '%');
    $paginator = Zend_Paginator::factory($playlistSelect);
    $paginator->setCurrentPageNumber($page);

    $data = array();
    foreach( $paginator as $playlist ) {
      $data[$playlist->playlist_id] = $playlist->getTitle();
    }
    $this->view->status = true;
    $this->view->data = $data;
  }

  public function infoAction()
  {
    $playlistIdentity = $this->_getParam('playlist_id');
    if( !$playlistIdentity ) {
      $this->view->status = false;
      return;
    }

    $playlist = Engine_Api::_()->getItem('music_playlist', $playlistIdentity);
    if( !$playlist ) {
      $this->view->status = false;
      return;
    }

    $this->view->status = true;
    $this->view->identity = $playlist->getIdentity();
    $this->view->title = $playlist->getTitle();
    $this->view->description = $playlist->getDescription();
    $this->view->href = $playlist->getHref();
    $this->view->photo = $playlist->getPhotoUrl('thumb.icon');
  }
}