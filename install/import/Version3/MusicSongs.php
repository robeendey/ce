<?php

class Install_Import_Version3_MusicSongs extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_music';

  protected $_toTable = 'engine4_music_playlist_songs';

  protected function _initPost()
  {
    $this->_truncateTable($this->getToDb(), 'engine4_music_playlists');
  }

  protected function _translateRow(array $data, $key = null)
  {
    // See if there is an existing playlist for this user
    $playlistIdentity = $this->getToDb()->select()
      ->from('engine4_music_playlists', 'playlist_id')
      ->where('owner_type = ?', 'user')
      ->where('owner_id = ?', $data['music_user_id'])
      ->limit(1)
      ->query()
      ->fetchColumn(0)
      ;

    // No playlist, make new one
    if( !$playlistIdentity ) {
      $this->getToDb()->insert('engine4_music_playlists', array(
        'title' => 'Profile Playlist',
        'description' => '',
        'owner_type' => 'user',
        'owner_id' => $data['music_user_id'],
        'search' => 1,
        'profile' => 1,
        'creation_date' => '0000-00-00 00:00', //$this->_translateTime(time()),
        'modified_date' => '0000-00-00 00:00', //$this->_translateTime(time()),
      ));
      $playlistIdentity = $this->getToDb()->lastInsertId();
    }


    
    // Insert file
    $file = $this->_getFromUserDir($data['music_user_id'], 'uploads_user',
        $data['music_id'] . '.' . $data['music_ext']);

    $fileIdentity = 0;
    if( file_exists($file) ) {
      try {
        $fileIdentity = (int) $this->_translateFile($file, array(
          'parent_type' => 'music_playlist_song',
          'parent_id' => $data['music_id'],
          'user_id' => 0,
        ));
      } catch( Exception $e ) {
        $this->_error($e);
        $fileIdentity = null;
      }
    }


    // search
    //if( @$newData['search'] ) {
      $this->_insertSearch('music_playlist_song', @$newData['song_id'], @$data['title']);
    //}
    
    // Insert song
    return array(
      'song_id' => $data['music_id'],
      'playlist_id' => $playlistIdentity,
      'file_id' => $fileIdentity,
      'title' => $data['music_title'],
      'play_count' => 0,
      'order' => $data['music_track_num'],
    );
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_music` (
  `music_id` int(10) unsigned NOT NULL auto_increment,
  `music_user_id` int(10) unsigned NOT NULL default '0',
  `music_track_num` int(10) unsigned NOT NULL default '0',
  `music_date` int(11) NOT NULL default '0',
  `music_title` varchar(64) collate utf8_unicode_ci NOT NULL default '',
  `music_ext` varchar(8) collate utf8_unicode_ci NOT NULL default '',
  `music_filesize` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`music_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_music_playlists` (
  `playlist_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(63) NOT NULL default '',
  `description` text NOT NULL,
  `photo_id` int(11) unsigned NOT NULL default '0',
  `owner_type` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  `search` tinyint(1) NOT NULL default '1',
  `profile` tinyint(1) NOT NULL default '0',
  `composer` tinyint(1) NOT NULL default '0',
  `creation_date` datetime NOT NULL,
  `modified_date` datetime NOT NULL,
  `play_count` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`playlist_id`),
  KEY `creation_date` (`creation_date`),
  KEY `play_count` (`play_count`),
  KEY `owner_id` (`owner_type`,`owner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_music_playlist_songs` (
  `song_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) unsigned NOT NULL,
  `file_id` int(11) unsigned NOT NULL,
  `title` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `play_count` int(11) unsigned NOT NULL default '0',
  `order` smallint(6) NOT NULL default '0',
  PRIMARY KEY (`song_id`),
  KEY (`playlist_id`,`file_id`),
  KEY `play_count` (`play_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * 
 */