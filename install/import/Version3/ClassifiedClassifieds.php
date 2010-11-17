<?php

class Install_Import_Version3_ClassifiedClassifieds extends Install_Import_Version3_Abstract
{
  protected $_fromTable = 'se_classifieds';

  protected $_toTable = 'engine4_classified_classifieds';

  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $newData['classified_id'] = $data['classified_id'];
    $newData['title'] = $data['classified_title'];
    $newData['body'] = $data['classified_body'];
    $newData['owner_id'] = $data['classified_user_id'];
    $newData['creation_date'] = $this->_translateTime($data['classified_date']);
    $newData['modified_date'] = $this->_translateTime($data['classified_dateupdated']);
    $newData['view_count'] = $data['classified_views'];
    $newData['comment_count'] = $data['classified_totalcomments'];
    $newData['search'] = $data['classified_search'];

    // get photo
    if( !empty($data['classified_photo']) ) {
      $file = $this->_getFromUserDir(
        $data['classified_id'],
        'uploads_classified',
        $data['classified_photo']
      );

      if( file_exists($file) ) {
        try {
          if( $this->getParam('resizePhotos', true) ) {
            $file_id = $this->_translatePhoto($file, array(
              'parent_type' => 'classified',
              'parent_id' => $data['classified_id'],
              'user_id' => @$data['classified_user_id'],
            ));
          } else {
            $file_id = $this->_translateFile($file, array(
              'parent_type' => 'classified',
              'parent_id' => $data['classified_id'],
              'user_id' => @$data['classified_user_id'],
            ), true);
          }
        } catch( Exception $e ) {
          $file_id = null;
          $this->_warning($e->getMessage(), 1);
        }

        if( $file_id ) {
          $newData['photo_id'] = $file_id;
        }
      }
    }

    // privacy
    $this->_insertPrivacy('classified', $data['classified_id'], 'view', $this->_translatePrivacy($data['classified_privacy'], 'owner'));
    $this->_insertPrivacy('classified', $data['classified_id'], 'comment', $this->_translatePrivacy($data['classified_privacy'], 'owner'));

    // search
    if( @$newData['search'] ) {
      $this->_insertSearch('classified', @$newData['classified_id'], @$newData['title'], @$newData['body']);
    }
    
    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_classifieds` (
*  `classified_id` int(10) unsigned NOT NULL auto_increment,
*  `classified_user_id` int(10) unsigned NOT NULL default '0',
  `classified_classifiedcat_id` int(10) unsigned NOT NULL default '0',
*  `classified_date` int(11) NOT NULL default '0',
*  `classified_dateupdated` int(11) NOT NULL default '0',
*  `classified_views` int(10) unsigned NOT NULL default '0',
*  `classified_title` varchar(128) collate utf8_unicode_ci NOT NULL default '',
*  `classified_body` text collate utf8_unicode_ci,
  `classified_photo` varchar(16) collate utf8_unicode_ci NOT NULL default '',
*  `classified_search` tinyint(3) unsigned NOT NULL default '0',
*  `classified_privacy` tinyint(3) unsigned NOT NULL default '0',
*  `classified_comments` tinyint(3) unsigned NOT NULL default '0',
*  `classified_totalcomments` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`classified_id`),
  KEY `INDEX` (`classified_user_id`,`classified_classifiedcat_id`),
  FULLTEXT KEY `SEARCH` (`classified_title`,`classified_body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE `engine4_classified_classifieds` (
*  `classified_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
*  `title` varchar(128) NOT NULL,
*  `body` longtext NOT NULL,
*  `owner_id` int(11) unsigned NOT NULL,
  `category_id` int(11) unsigned NOT NULL,
  `photo_id` int(10) unsigned NOT NULL default '0',
*  `creation_date` datetime NOT NULL,
*  `modified_date` datetime NOT NULL,
*  `view_count` int(11) unsigned NOT NULL default '0',
*  `comment_count` int(11) unsigned NOT NULL default '0',
*  `search` tinyint(1) NOT NULL default '1',
  `closed` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`classified_id`),
  KEY `owner_id` (`owner_id`),
  KEY `search` (`search`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci ;
 *
 */








/*
CREATE TABLE IF NOT EXISTS `se_classifiedstyles` (
  `classifiedstyle_id` int(11) NOT NULL auto_increment,
  `classifiedstyle_user_id` int(11) NOT NULL default '0',
  `classifiedstyle_css` text collate utf8_unicode_ci,
  PRIMARY KEY  (`classifiedstyle_id`),
  KEY `INDEX` (`classifiedstyle_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

