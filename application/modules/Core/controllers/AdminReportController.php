<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminReportController.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminReportController extends Core_Controller_Action_Admin
{
  
  public function init()
  {
    if( !defined('_ENGINE_ADMIN_NEUTER') || !_ENGINE_ADMIN_NEUTER ) {
      $this->_helper->requireUser();
    }
  }

  public function indexAction()
  {
    $this->view->formFilter = $formFilter = new Core_Form_Admin_Filter();
    $page = $this->_getParam('page',1);
    if( $this->getRequest()->isPost() && $formFilter->isValid($this->getRequest()->getPost()) )
    {
      $values = $formFilter->getValues();
      $this->view->paginator = $paginator = Engine_Api::_()->getApi('Report', 'core')->getPaginator($values);
      if ($values['orderby']&& $values['orderby_direction']!='ASC') $this->view->orderby = $values['orderby'];
    }
    else $paginator = Engine_Api::_()->getApi('Report', 'core')->getPaginator();

    $this->view->paginator = $paginator->setCurrentPageNumber( $page );
  }

  public function viewAction()
  {
    // first get the item and then redirect admin to the item page
    $this->view->id = $id = $this->_getParam('id', null);
    $report = Engine_Api::_()->getItem('core_report', $id);
    $item = Engine_Api::_()->getItem($report->subject_type, $report->subject_id);
    $this->_redirectCustom($item->getHref());
  }
  
  public function deleteAction()
  {
    $this->view->id = $id = $this->_getParam('id', null);
    $report = Engine_Api::_()->getItem('core_report', $id);

    // Save values
    if( $this->getRequest()->isPost() )
    {
      $report->delete();
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
      //$form->addMessage('Changes Saved!');
    }
  }

  public function deleteselectedAction()
  {
    //$this->view->form = $form = new Announcement_Form_Admin_Edit();
    $this->view->ids = $ids = $this->_getParam('ids', null);
    $confirm = $this->_getParam('confirm', false);
    $this->view->count = count(explode(",", $ids));

    //$announcement = Engine_Api::_()->getItem('announcement', $id);

    // Save values
    if( $this->getRequest()->isPost() && $confirm == true )
    {
      $ids_array = explode(",", $ids);
      foreach ($ids_array as $id){
        $report = Engine_Api::_()->getItem('core_report', $id);
        if( $report ) {
          $report->delete();
        }
      }

      //$announcement->delete();
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

  }
}