<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Encode.php 7260 2010-09-01 23:42:02Z jung $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Music_Plugin_Task_Maintenance_Cleanup extends Core_Plugin_Task_Abstract
{
  public function execute()
  {
    $tbl_songs      = Engine_Api::_()->getDbTable('playlistSongs', 'music');
    $tbl_songs_name = $tbl_songs->info('name');
    $tbl_files      = Engine_Api::_()->getDbTable('files', 'storage');
    $tbl_files_name = $tbl_files->info('name');

    // find songs in the storage_files table that don't exist in music_playlist_songs
    $select = $tbl_files->select()
                        ->setIntegrityCheck(false)
                        ->from($tbl_files_name, 'file_id')
                        ->joinLeft($tbl_songs_name, "$tbl_files_name.file_id = $tbl_songs_name.file_id", '')
                        ->where('type = ?', 'song')
                        ->where('parent_type = ?', 'music_song')
                        ->where('song_id IS NULL')
                        ->limit(50)
                        ;
    $rows = $tbl_files->fetchAll($select);
    if ($rows) {
      foreach ($rows as $row) {
        $db = $tbl_songs->getAdapter();
        $db->beginTransaction();
        try {
          Engine_Api::_()->getItem('storage_file', $row->file_id)->remove();
          $db->commit();
        } catch (Exception $e) {
          $db->rollback();
          throw $e;
        }
      }
    }
    return;
  }

}