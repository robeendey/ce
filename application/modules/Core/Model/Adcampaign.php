<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Adcampaign.php 7452 2010-09-23 03:21:59Z steve $
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Model_Adcampaign extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getAds()
  {
    $table = Engine_Api::_()->getDbtable('Ads', 'core');
    $select = $table->select()->where('ad_campaign = ?', $this->adcampaign_id);
    return $table->fetchAll($select);
  }

  public function getAd()
  {
    $table = Engine_Api::_()->getDbtable('Ads', 'core');
    $select = $table->select()->where('ad_campaign = ?', $this->adcampaign_id)->order('views ASC');
    return $table->fetchRow($select);
  }

  public function allowedToView($viewer)
  {
    $allowed = false;
    if( $viewer->getIdentity() ) {
      // check if the user level is among the selected level
      $selected_levels = Zend_Json_Decoder::decode($this->level);
      $selected_networks = Zend_Json_Decoder::decode($this->network);

      // @todo network is not supposed to be false
      $user_networks = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfIds($viewer, null);

      if( @in_array($viewer->level_id, $selected_levels) || @array_intersect($user_networks, $selected_networks) ) {
        $allowed = true;
      }
    }

    return $allowed;
  }

  public function checkLimits()
  {
    $allowed = false;

    // if limits are equal to zero, ignore the limit checks

    if( !empty($this->limit_view) && $this->limit_view < $this->views ) {
      $allowed = true;
    }
    if( !empty($this->limit_click) && $this->limit_click < $this->clicks ) {
      $allowed = true;
    }
    if( !empty($this->limit_ctr) && $this->limit_ctr > ($this->clicks / $this->views * 100) ) {
      $allowed = true;
    }

    return $allowed;
  }

  public function checkStarted()
  {
    $allowed = false;

    if( time() < strtotime($this->start_time) ) {
      $allowed = true;
    }
    return $allowed;
  }

  public function checkExpired()
  {
    $allowed = false;

    if( $this->end_settings == 1 && time() > strtotime($this->end_time) ) {
      $allowed = true;
    }
    return $allowed;
  }

}