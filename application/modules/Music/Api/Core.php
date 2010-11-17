<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_Api_Core extends Core_Api_Abstract
{
  // handle song upload
  public function createSong($file, $params=array())
  {
    // upload to storage system
    $song_path = pathinfo($file['name']);
    $params    = array_merge(array(
      'type'        => 'song',
      'name'        => $file['name'],
      'parent_type' => 'music_song',
      'parent_id'   => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'user_id'     => Engine_Api::_()->user()->getViewer()->getIdentity(),
      'extension'   => substr($file['name'], strrpos($file['name'], '.')+1),
    ), $params);

    $song = Engine_Api::_()->storage()->create($file, $params);
    return $song;
  }


  public function getPlaylistSelect($params = array()) 
  {
    $ps_table = Engine_Api::_()->getDbTable('playlistSongs', 'music');
    $ps_name  = $ps_table->info('name');
    $p_table  = Engine_Api::_()->getDbTable('playlists', 'music');
    $p_name   = $p_table->info('name');
    
    $select   = $p_table->select()
                        ->from($p_table)
                        ->group("$p_name.playlist_id");

    // WALL SEARCH
    if (!empty($params['wall']))
      $select->where('composer = 1');

    // USER SEARCH
    if (!empty($params['user'])) {
      if (is_object($params['user']))
        $select->where('owner_id = ?', $params['user']->getIdentity());
      elseif (is_numeric($params['user']))
        $select->where('owner_id = ?', $params['user']);
    } else {
      $select->where('search = 1')
             // prevent empty playlists from showing
             ->joinLeft($ps_name, "$p_name.playlist_id = $ps_name.playlist_id", '')
             ->where("$ps_name.song_id IS NOT NULL")
             ;
    }

    // SORT
    if (!empty($params['sort'])) {
      $sort = $params['sort'];
      if ('recent' == $sort)
        $select->order('creation_date DESC');
      elseif ('popular' == $sort)
        $select->order("$p_name.play_count DESC");
    }

    // STRING SEARCH
    if (!empty($params['search']))
      $select
          ->where("$p_name.title LIKE ?", "%{$params['search']}%")
          ->orWhere("$p_name.description LIKE ?", "%{$params['search']}%")
          ->joinLeft($ps_name, "$p_name.playlist_id = $ps_name.playlist_id", '')
          ->orWhere("$ps_name.title LIKE ?", "%{$params['search']}%")
          ;
    
    return $select;
  }

  public function getPlaylistPaginator($params = array())
  {
    $paginator = Zend_Paginator::factory($this->getPlaylistSelect($params));
    if( !empty($params['page']) )
    {
      $paginator->setCurrentPageNumber($params['page']);
    }
    if( !empty($params['limit']) )
    {
      $paginator->setItemCountPerPage($params['limit']);
    }
    return $paginator;
  }

  public function getPlaylistRows($params = array())
  {
    return Engine_Api::_()->getDbTable('playlists', 'music')->fetchAll( $this->getPlaylistSelect($params) );
  }
}