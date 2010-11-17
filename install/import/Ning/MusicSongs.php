<?php

class Install_Import_Ning_MusicSongs extends Install_Import_Ning_Abstract
{
  protected $_fromFile = 'ning-music-local.json';

  protected $_fromFileAlternate = 'ning-music.json';

  protected $_toTable = 'engine4_music_playlist_songs';

  protected function  _translateRow(array $data, $key = null)
  {
    $userIdentity = $this->getUserMap($data['contributorName']);
    $songIdentity = $key + 1;

    // Make file
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $data['audioUrl'];
    $file_id = $this->_translateFile($file, array(
      'parent_type' => 'music_playlist_song',
      'parent_id' => $songIdentity,
      //'user_id' => $userIdentity,
    ));

    // Make/get playlist
    $playlist_id = $this->getToDb()->select()
      ->from('engine4_music_playlists', 'playlist_id')
      ->where('owner_id = ?', $userIdentity)
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    if( !$playlist_id ) {
      $this->getToDb()->insert('engine4_music_playlists', array(
        'title' => 'Profile Playlist',
        'owner_type' => 'user',
        'owner_id' => $userIdentity,
        'search' => 1,
        'profile' => 1,
        'creation_date' => $this->_translateTime($data['createdDate']),
        'modified_date' => $this->_translateTime($data['updatedDate']),
      ));
      $playlist_id = $this->getToDb()->lastInsertId();
    }
    
    $newData = array();

    $newData['song_id'] = $songIdentity;
    $newData['playlist_id'] = $playlist_id;
    $newData['file_id'] = $file_id;
    $newData['title'] = $data['title'];
    
    return $newData;
  }
}