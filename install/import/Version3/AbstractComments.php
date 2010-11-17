<?php

abstract class Install_Import_Version3_AbstractComments extends Install_Import_Version3_Abstract
{
  protected $_toTable = 'engine4_core_comments';

  protected $_fromResourceType;

  protected $_toResourceType;

  protected $_priority = 90;

  public function __sleep()
  {
    return array_merge(parent::__sleep(), array(
      '_fromResourceType', '_toResourceType'
    ));
  }

  public function getFromResourceType()
  {
    if( null === $this->_fromResourceType ) {
      throw new Engine_Exception('No resource type');
    }
    return $this->_fromResourceType;
  }

  public function getToResourceType()
  {
    if( null === $this->_toResourceType ) {
      throw new Engine_Exception('No resource type');
    }
    return $this->_toResourceType;
  }

  protected function _initPre()
  {
    $this->_fromTable = 'se_' . $this->getFromResourceType() . 'comments';
  }
  
  protected function  _translateRow(array $data, $key = null)
  {
    $newData = array();

    $fromType = $this->getFromResourceType();

    $newData['resource_type'] = $this->getToResourceType();
    if( $fromType == 'blog' ) {
      $newData['resource_id'] = $data['blogcomment_blogentry_id'];
    } elseif( $fromType == 'profile' ) {
      $newData['resource_id'] = $data['profilecomment_user_id'];
    } else {
      $newData['resource_id'] = $data[$fromType . 'comment_' . $fromType . '_id'];
    }
    $newData['poster_type'] = 'user';
    $newData['poster_id'] = $data[$fromType . 'comment_authoruser_id'];
    $newData['body'] = $data[$fromType . 'comment_body'];
    $newData['creation_date'] = $this->_translateTime($data[$fromType . 'comment_date']);

    // Add activity as well for: user, event, group
    if( in_array($this->getToResourceType(), array('user', 'event', 'group')) ) {
      // Type hack
      if( $this->getToResourceType() == 'user' &&
          $newData['resource_type'] == 'user' &&
          $newData['resource_type'] == $newData['poster_type'] &&
          $newData['resource_id'] == $newData['poster_id'] ) {
        $type = 'status';
      } else {
        $type = 'post';
      }

      // Get privacy
      $privacyTypes = $this->getToDb()->select()
        ->from('engine4_authorization_allow', 'role')
        ->where('resource_type = ?', $this->getToResourceType())
        ->where('resource_id = ?', $newData['resource_id'])
        ->where('action = ?', 'view')
        ->query()
        ->fetchAll();
      foreach( $privacyTypes as &$privacyType ) {
        $privacyType = $privacyType['role'];
      }

      $this->getToDb()->insert('engine4_activity_actions', array(
        'type' => $type,
        'subject_type' => $newData['poster_type'],
        'subject_id' => $newData['poster_id'],
        'object_type' => $newData['resource_type'],
        'object_id' => $newData['resource_id'],
        'body' => $newData['body'],
        'date' => $newData['creation_date'],
      ));
      $action_id = $this->getToDb()->lastInsertId();

      // Insert into stream table
      $targetTypes = array();

      // @todo this does not work for inherited permissions, i.e. album photos
      if( in_array('everyone', $privacyTypes) ) {
        $targetTypes['everyone'] = 0;
      }
      if( in_array('registered', $privacyTypes) ) {
        $targetTypes['registered'] = 0;
      }

      // Not implemented - need to get parent here
      //if( in_array('owner_member', $privacyTypes) ) {
      //
      //}
      
      if( in_array($this->getToResourceType(), array('user')) ) {
        $targetTypes['owner'] = $newData['resource_id'];
        $targetTypes['parent'] = $newData['resource_id'];
        // Let's just assume they allow their friends to view
        //if( in_array('member', $privacyTypes) || in_array('members', $privacyTypes) ) {
          $targetTypes['members'] = $newData['resource_id'];
        //}
        // Skip network for now
        //if( in_array('network', $privacyTypes) || in_array('networks', $privacyTypes) ) {
        //  $targetTypes['network'] = $newData['resource_id'];
        //}
      }
      if( in_array($this->getToResourceType(), array('event', 'group')) ) {
        // Let's just assume they allow their members to view
        //if( in_array('member', $privacyTypes) || in_array('members', $privacyTypes) ) {
          $targetTypes[$newData['resource_type']] = $newData['resource_id'];
        //}
      }

      foreach( $targetTypes as $targetType => $targetIdentity ) {
        try {
          $this->getToDb()->insert('engine4_activity_stream', array(
            'target_type' => $targetType,
            'target_id' => $targetIdentity,
            'subject_type' => $newData['poster_type'],
            'subject_id' => $newData['poster_id'],
            'object_type' => $newData['resource_type'],
            'object_id' => $newData['resource_id'],
            'type' => $type,
            'action_id' => $action_id,
          ));
        } catch( Exception $e ) {
          $this->_error('Problem adding activity privacy: ' . $e->getMessage());
        }
      }
    }



    return $newData;
  }
}

/*
CREATE TABLE IF NOT EXISTS `se_videocomments` (
  `videocomment_id` int(10) unsigned NOT NULL auto_increment,
*  `videocomment_video_id` int(10) unsigned NOT NULL,
*  `videocomment_authoruser_id` int(9) unsigned default NULL,
*  `videocomment_date` int(14) NOT NULL default '0',
*  `videocomment_body` text collate utf8_unicode_ci,
  PRIMARY KEY  (`videocomment_id`),
  KEY `INDEX` (`videocomment_video_id`,`videocomment_authoruser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
 *
 */

/*
CREATE TABLE IF NOT EXISTS `engine4_core_comments` (
  `comment_id` int(11) unsigned NOT NULL auto_increment,
*  `resource_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `resource_id` int(11) unsigned NOT NULL,
*  `poster_type` varchar(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
*  `poster_id` int(11) unsigned NOT NULL,
*  `body` text NOT NULL,
*  `creation_date` datetime NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `resource_type` (`resource_type`,`resource_id`),
  KEY `poster_type` (`poster_type`, `poster_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;
 * 
 */