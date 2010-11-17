<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Report.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Api_Report extends Core_Api_Abstract
{
  public function getPaginator($params = array())
  {
    return Zend_Paginator::factory($this->getSelect($params));
  }

  public function getSelect($params = array())
  {
    $table = $this->api()->getDbtable('reports', 'core');
    
    $select = $table->select()
      ->order( !empty($params['orderby']) ? $params['orderby'].' '.$params['orderby_direction'] : 'creation_date DESC' );

    $select->limit(10);

    return $select;
  }
}