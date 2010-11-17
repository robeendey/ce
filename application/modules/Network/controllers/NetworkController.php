<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: NetworkController.php 7306 2010-09-07 07:33:09Z john $
 * @author     Sami
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Network
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Network_NetworkController extends Core_Controller_Action_Standard
{
   public function suggestAction()
   {
    if( $this->_helper->requireUser()->checkRequire() )
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $table = Engine_Api::_()->getItemTable('network');
      $select = $table->select()
        ->where('assignment = ?', 0)
        ->where('hide = ?', 0);

      if( null !== ($text = $this->_getParam('text', $this->_getParam('text'))))
      {
        $select->where('`'.$table->info('name').'`.`title` LIKE ?', '%'. $text .'%');
      }

      $data = array();
      foreach( $table->fetchAll($select) as $network )
      {
        if( !$network->membership()->isMember($viewer) )
        {
          $data[] = array(
            'id' => $network->getIdentity(),
            'title' => $network->getTitle(),
            //'title' => $this->view->translate($network->getTitle()),
          );
        }
      }
    }
    
    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }
}