<?php
/**
 * SocialEngine
 *
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Controller.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */

/**
 * @category   Application_Widget
 * @package    Rss
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Widget_RssController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $url = $this->_getParam('url');
    if( !$url ) {
      return $this->setNoRender();
    }
    $this->view->url = $url;

    // Zend_Feed requires DOMDocument
    if( !class_exists('DOMDocument', false) ) {
      return $this->setNoRender();
    }

    $rss = Zend_Feed::import($url);
    $channel = array(
      'title'       => $rss->title(),
      'link'        => $rss->link(),
      'description' => $rss->description(),
      'items'       => array()
    );

    $max = $this->_getParam('max', 4);
    $count = 0;

    // Loop over each channel item and store relevant data
    foreach( $rss as $item )
    {
      if( $count++ >= $max ) break;
      $channel['items'][] = array(
        'title'       => $item->title(),
        'link'        => $item->link(),
        'description' => $item->description(),
        'pubDate'     => $item->pubDate(),
        'guid'        => $item->guid(),
      );
    }

    $this->view->channel = $channel;
  }
}