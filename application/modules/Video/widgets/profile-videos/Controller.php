<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Video_Widget_ProfileVideosController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Get paginator
    $profile_owner_id = $subject->getIdentity();
    $this->view->paginator = $paginator = Engine_Api::_()->video()->getVideosPaginator(array(
      'user_id' => $profile_owner_id,
      'status' => 1,
      'search' => 1
    ));
    $paginator->setItemCountPerPage(16);
    //$paginator->setCurrentPageNumber(1);


    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    } else {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->items_per_page = $settings->getSetting('video_page', 10);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}