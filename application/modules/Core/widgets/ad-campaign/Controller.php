<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7452 2010-09-23 03:21:59Z steve $
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Widget_AdCampaignController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $id = $this->_getParam('adcampaign_id');
    $this->view->campaign = $campaign = Engine_Api::_()->getItem('core_adcampaign', $id);
    if($campaign){
      $this->view->ad= $ad = $campaign->getAd();
      $viewer = Engine_Api::_()->user()->getViewer();

      // check if ad is active
      if(!$ad || !$campaign->status){
        $this->_noRender = true;
      }

      // check if user is the audience
      else if(!($campaign->public && !$viewer->getIdentity()) && !$campaign->allowedToView($viewer)){
        $this->_noRender = true;
      }

      // check if exeeded limits
      else if($campaign->checkLimits()){
        $this->_noRender = true;
      }

      // check if campaign started
      else if($campaign->checkStarted()){
        $this->_noRender = true;
      }

      // check if campaign expired
      else if($campaign->checkExpired()){
        $this->_noRender = true;
      }

      // all clear, incremement views and render
      else{
        $campaign->views++;
        $campaign->save();
        $ad->views++;
        $ad->save();
     }
    }
    else {
      $this->_noRender = true;
    }
  }
}