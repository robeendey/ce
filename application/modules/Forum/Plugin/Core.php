<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Core.php 7481 2010-09-27 08:41:01Z john $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Forum_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('topics', 'forum');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'forum topic');
  }
  
  public function onUserDeleteAfter($event)
  {
    $payload = $event->getPayload();
    $user_id = $payload['identity'];

    // Signatures
    $table = Engine_Api::_()->getDbTable('signatures', 'forum');
    $table->delete(array(
      'user_id = ?' => $user_id,
    ));

    // Moderators
    $table = Engine_Api::_()->getDbTable('listItems', 'forum');
    $select = $table->select()->where('child_id = ?', $user_id);
    $rows = $table->fetchAll($select);
    foreach( $rows as $row ) {
      $row->delete();
    }

    // Topics
    $table = Engine_Api::_()->getDbTable('topics', 'forum');
    $select = $table->select()->where('user_id = ?', $user_id);
    $rows = $table->fetchAll($select);
    foreach( $rows as $row ) {
      //$row->delete();
    }

    // Posts
    $table = Engine_Api::_()->getDbTable('posts', 'forum');
    $select = $table->select()->where('user_id = ?', $user_id);
    $rows = $table->fetchAll($select);
    foreach ($rows as $row)
    {
      //$row->delete();
    }

    // Topic views
    $table = Engine_Api::_()->getDbTable('topicviews', 'forum');
    $table->delete(array(
      'user_id = ?' => $user_id,
    ));
  }
}
