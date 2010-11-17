<?php

class Install_Import_Version3_VideoVideos extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_videos';

  protected $_toTable = 'engine4_video_videos';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['video_id'] = $data['video_id'];
    $newData['title'] = $data['video_title'];
    $newData['description'] = $data['video_desc'];
    $newData['search'] = $data['video_search'];
    $newData['owner_type'] = 'user';
    $newData['owner_id'] = $data['video_user_id'];
    $newData['view_count'] = $data['video_views'];
    $newData['comment_count'] = $data['video_totalcomments'];
    $newData['creation_date'] = $this->_translateTime($data['video_datecreated']);
    $newData['modified_date'] = $this->_translateTime($data['video_dateupdated']);
    $newData['rating'] = $data['video_cache_rating'];
    $newData['duration'] = (string) @$data['video_duration_in_sec'];
    $newData['code'] = (string) @$data['video_youtube_code'];

    // video type
    if( $data['video_type'] == 1 ) {
      $newData['type'] = 1;
    } else {
      $newData['type'] = 3;
    }

    // video status
    if( $newData['type'] != 3 ) {
      $newData['status'] = 1;
    } else {
      if( !$data['video_uploaded'] || !$data['video_is_converted'] ) {
        $newData['status'] = 3;
      } else {
        $newData['status'] = 1;
      }
    }

    // ----- youtube -----
    if( !empty($newData['code']) ) {
      //$thumb_url = $this->_getYoutubeThumb($newData['code']);
      $thumb_url = $this->_getFromUserDir(
        $data['video_user_id'],
        'uploads_video',
        $data['video_id'] . '_thumb.jpg'
      );
      $tmpFile = tempnam(APPLICATION_PATH . '/temporary', $newData['code']) . '.jpg';
      $fh1 = @fopen($thumb_url, 'r');
      $fh2 = @fopen($tmpFile, 'w');
      if( $fh1 && $fh2 ) {
        stream_copy_to_stream($fh1, $fh2);
      }
      @fclose($fh1);
      @fclose($fh2);
      if( file_exists($tmpFile) && filesize($tmpFile) > 0 ) {
        try {
          if( $this->getParam('resizePhotos', true) ) {
            $thumb_file_id = $this->_translatePhoto($tmpFile, array(
              'parent_type' => 'video',
              'parent_id' => $data['video_id'],
              'user_id' => $data['video_user_id'],
            ));
          } else {
            $thumb_file_id = $this->_translateFile($tmpFile, array(
              'parent_type' => 'video',
              'parent_id' => $data['video_id'],
              'user_id' => $data['video_user_id'],
            ), true);
          }
        } catch( Exception $e ) {
          $file_id = null;
          $this->_warning($e->getMessage(), 1);
        }
        if( $thumb_file_id ) {
          $newData['photo_id'] = $thumb_file_id;
        }
      }
    }

    // ----- video file -----
    else {
      $file = $this->_getFromUserDir(
        $data['video_user_id'],
        'uploads_video',
        $data['video_id'] . '.' . 'flv'
      );

      if( !file_exists($file) ) {
        $newData['status'] = 3; // error
      } else {
        try {
          $file_id = $this->_translateFile($file, array(
            'parent_type' => 'video',
            'parent_id' => $data['video_id'],
            'user_id' => $data['video_user_id'],
          ), false);
        } catch( Exception $e ) {
          $file_id = null;
          $this->_error($e);
        }

        if( !$file_id ) {
          $newData['status'] = 3; // error
        } else {
          $newData['file_id'] = $file_id;

          // video thumb
          $thumbFile = $this->_getFromUserDir(
            $data['video_user_id'],
            'uploads_video',
            $data['video_id'] . '_thumb.jpg'
          );

          if( file_exists($thumbFile) ) {
            try {
              if( $this->getParam('resizePhotos', true) ) {
                $thumb_file_id = $this->_translatePhoto($thumbFile, array(
                  'parent_type' => 'video',
                  'parent_id' => $data['video_id'],
                  'user_id' => $data['video_user_id'],
                ));
              } else {
                $thumb_file_id = $this->_translateFile($thumbFile, array(
                  'parent_type' => 'video',
                  'parent_id' => $data['video_id'],
                  'user_id' => $data['video_user_id'],
                ), true);
              }
            } catch( Exception $e ) {
              $thumb_file_id = null;
              $this->_error($e);
            }

            if( $thumb_file_id ) {
              $newData['photo_id'] = $thumb_file_id;
            }
          }
        }
      }
    }


    // privacy
    try {
      $this->_insertPrivacy('video', $data['video_id'], 'view', $this->_translatePrivacy($data['video_privacy'], 'owner'));
      $this->_insertPrivacy('video', $data['video_id'], 'comment', $this->_translatePrivacy($data['video_comments'], 'owner'));
    } catch( Exception $e ) {
      $this->_error('Problem adding privacy options for object id ' . $data['video_id'] . ' : ' . $e->getMessage());
    }
    
    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('video', @$newData['video_id'], @$newData['title'], @$newData['description']);
    }
    
    return $newData;
  }

  protected function _getYoutubeThumb($code)
  {
    return "http://img.youtube.com/vi/{$code}/default.jpg";
  }

  protected function _getVimeoThumb($code)
  {
    $data = simplexml_load_file("http://vimeo.com/api/v2/video/{$code}.xml");
    $thumbnail = $data->video->thumbnail_medium;
    return $thumbnail;
  }
}

/*
type
1 = youtube
2 = vimeo
3 = computer

status
0 = video in queue to be processed
1 = successful (ready to view)
2 = in process of conversion
3 = failed (general reason)
4 = failed (ffmpeg not support)
5 = failed (audio file)
7 = site limit
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `se_videos` (
*  `video_id` int(9) unsigned NOT NULL auto_increment,
*  `video_user_id` int(9) unsigned NOT NULL default '0',
*  `video_datecreated` int(14) NOT NULL default '0',
*  `video_title` varchar(255) collate utf8_unicode_ci default NULL,
*  `video_desc` text collate utf8_unicode_ci,
*  `video_views` smallint(5) unsigned NOT NULL default '0',
*  `video_cache_rating` float NOT NULL default '0',
-  `video_cache_rating_weighted` float NOT NULL default '0',
-  `video_cache_rating_total` int(3) unsigned NOT NULL default '0',
*  `video_duration_in_sec` smallint(4) unsigned default NULL,
*  `video_is_converted` tinyint(1) NOT NULL default '0',
*  `video_privacy` int(2) default NULL,
*  `video_comments` int(2) default NULL,
*  `video_search` tinyint(1) unsigned default '1',
*  `video_totalcomments` smallint(5) unsigned default '0',
*  `video_type` tinyint(1) NOT NULL default '0',
*  `video_youtube_code` varchar(50) collate utf8_unicode_ci default NULL,
*  `video_dateupdated` int(14) NOT NULL default '0',
*  `video_uploaded` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`video_id`),
  KEY `video_cache_rating` (`video_cache_rating`),
  KEY `video_views` (`video_views`),
  FULLTEXT KEY `title_and_text` (`video_title`,`video_desc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 * 
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_video_videos` (
*  `video_id` int(11) unsigned NOT NULL auto_increment,
*  `title` varchar(100) NOT NULL,
*  `description` text NOT NULL,
*  `search` tinyint(1) NOT NULL default '1',
*  `owner_type` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `owner_id` int(11) NOT NULL,
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
*  `type` tinyint(1) NOT NULL,
*  `code` varchar(150) NOT NULL,
*  `photo_id` int(11) unsigned default NULL,
*  `rating` float NOT NULL,
-  `category_id` int(11) unsigned NOT NULL default '0',
*  `status` tinyint(1) NOT NULL,
*  `file_id` int(11) unsigned NOT NULL,
*  `duration` int(9) unsigned NOT NULL,
  PRIMARY KEY  (`video_id`),
  KEY `owner_id` (`owner_id`,`owner_type`),
  KEY `search` (`search`),
  KEY `creation_date` (`creation_date`),
  KEY `view_count` (`creation_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 * 
 */







/*
CREATE TABLE IF NOT EXISTS `engine4_video_categories` (
  `category_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `category_name` varchar(128) NOT NULL,
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 * 
 */