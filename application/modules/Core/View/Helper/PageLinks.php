<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PageLinks.php 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_View_Helper_PageLinks extends Engine_View_Helper_HtmlLink
{
  public function pageLinks($topic)
  {
    $last_page = $topic->getLastPage(25);
    if ($last_page == 1)
    {
      return "";
    }
    $return_value = '<span class="forum_pagelinks">';

    $pages = array();
    foreach (array(1,2,3, $last_page - 2, $last_page - 1, $last_page) as $page)
    {
      $pages[$page] = True;
    }
    foreach ($pages as $page_id=>$meaningless_bit) {
      if (($page_id > 0) && ($page_id <= $last_page)) {
        $return_value .=  $this->htmlLink($topic->getHref(array('page'=>$page_id)), $page_id) . ' ';
        if (($page_id == 3) && ($last_page > 6))
        {
          $return_value .= "...";
        }
      }
    }
    $return_value .= "</span>";
    return $return_value;
  }
}