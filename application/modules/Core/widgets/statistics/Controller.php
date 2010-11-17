<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Widget_StatisticsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // members, friends
    $table  = Engine_Api::_()->getDbTable('users', 'user');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array(
          'COUNT(*) AS count',
          'SUM(member_count) AS friends'))
      ->where('enabled = ?', 1);
    $rows = $table->fetchAll($select)->toArray();
    $this->view->member_count = $rows[0]['count'];
    $this->view->friend_count = $rows[0]['friends'];

    $friendship_types = Engine_Api::_()->getDbtable('membership', 'user');
    if ($friendship_types->isReciprocal())
      $this->view->friend_count = round($rows[0]['friends']/2);

    // posts
    $table  = Engine_Api::_()->getDbTable('actions', 'activity');
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), array('COUNT(*) AS count'));
    $rows   = $table->fetchAll($select)->toArray();
    $this->view->post_count = $rows[0]['count'];

    // comments
    $table  = Engine_Api::_()->getDbTable('comments', 'activity');
    $select = $table->select()
                    ->setIntegrityCheck(false)
                    ->from($table->info('name'), array(
                        'COUNT(*) AS count'));
    $rows   = $table->fetchAll($select)->toArray();
    $this->view->comment_count = $rows[0]['count'];

    // plugin hook
    $this->view->hooked_stats = array();
    $events     = Engine_Hooks_Dispatcher::getInstance()->callEvent('onStatistics');
    $events_res = $events->getResponses();
    if (is_array($events_res))
      $this->view->hooked_stats = $events_res;
  }

  public function getCacheKey()
  {
    return Zend_Registry::get('Locale')->toString();
  }
}