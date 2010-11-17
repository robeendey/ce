<?php

class Install_Import_Version3_VideoRatings extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_videoratings';

  protected $_toTable = 'engine4_video_ratings';
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['video_id'] = $data['videorating_video_id'];
    $newData['user_id'] = $data['videorating_user_id'];
    $newData['rating'] = $data['videorating_rating'];

    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_videoratings` (
*  `videorating_video_id` int(10) unsigned NOT NULL,
*  `videorating_user_id` int(9) unsigned NOT NULL,
*  `videorating_rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`videorating_video_id`,`videorating_user_id`),
  KEY `INDEX` (`videorating_video_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_video_ratings` (
*  `video_id` int(10) unsigned NOT NULL,
*  `user_id` int(9) unsigned NOT NULL,
*  `rating` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`video_id`,`user_id`),
  KEY `INDEX` (`video_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
 *
 */