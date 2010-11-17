<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: AdminContentController.php 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_AdminContentController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    // Get available content blocks
    $this->view->contentAreas = $contentAreas = $this->buildCategorizedContentAreas($this->getContentAreas());

    // Re-index by name
    $contentByName = array();
    foreach( $contentAreas as $category => $categoryAreas ) {
      foreach( $categoryAreas as $info ) {
        $contentByName[$info['name']] = $info;
      }
    }
    $this->view->contentByName = $contentByName;

    // Get page param
    $page = $this->_getParam('page', 'core_index_index');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    
    // Get current page
    $this->view->pageObject = $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
    if( null === $pageObject ) {
      $page = 'core_index_index';
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page));
      if( null === $pageObject ) {
        throw new Engine_Exception('Home page is missing');
      }
    }
    $this->view->page = $page;
    $this->view->pageObject = $pageObject;

    // Make page form
    $this->view->pageForm = $pageForm = new Core_Form_Admin_Layout_Content_Page();
    $pageForm->populate($pageObject->toArray());

    // Get page list
    $this->view->pageList = $pageList = $pageTable->fetchAll();

    // Get registered content areas
    $contentRowset = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $pageObject->page_id));
    $contentStructure = $pageTable->prepareContentArea($contentRowset);

    // Validate structure
    // Note: do not validate for header or footer
    $error = false;
    if( $pageObject->name !== 'header' && $pageObject->name !== 'footer' ) {
      foreach( $contentStructure as &$info1 ) {
        if( !in_array($info1['name'], array('top', 'bottom', 'main')) || $info1['type'] != 'container' ) {
          $error = true;
          break;
        }
        foreach( $info1['elements'] as &$info2 ) {
          if( !in_array($info2['name'], array('left', 'middle', 'right')) || $info1['type'] != 'container' ) {
            $error = true;
            break;
          }
        }
        // Re order second-level elements
        usort($info1['elements'], array($this, '_reorderContentStructure'));
      }
    }

    if( $error ) {
      throw new Exception('page failed validation check');
    }

    // Assign structure
    $this->view->contentRowset = $contentRowset;
    $this->view->contentStructure = $contentStructure;
  }

  public function updateAction()
  {
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $db = $pageTable->getAdapter();
    $db->beginTransaction();

    try {

      // Get page
      $page = $this->_getParam('page');
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
      if( null === $pageObject ) {
        throw new Engine_Exception('Page is missing');
      }

      // Update layout
      if( null !== ($newLayout = $this->_getParam('layout')) ) {
        $pageObject->layout = $newLayout;
        $pageObject->save();
      }

      // Get registered content areas
      $contentRowset = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $pageObject->page_id));

      // Get structure
      $strucure = $this->_getParam('structure');

      // Diff
      $orderIndex = 1;
      $newRowsByTmpId = array();
      $existingRowsByContentId = array();

      foreach( $strucure as $element ) {

        // Get info
        $content_id = @$element['identity'];
        $tmp_content_id = @$element['tmp_identity'];
        $parent_id = @$element['parent_identity'];
        $tmp_parent_id = @$element['parent_tmp_identity'];

        $newOrder = $orderIndex++;

        // Sanity
        if( empty($content_id) && empty($tmp_content_id) ) {
          throw new Exception('content id and tmp content id both empty');
        }
        //if( empty($parent_id) && empty($tmp_parent_id) ) {
        //  throw new Exception('parent content id and tmp parent content id both empty');
        //}

        // Get existing content row (if any)
        $contentRow = null;
        if( !empty($content_id) ) {
          $contentRow = $contentRowset->getRowMatching('content_id', $content_id);
          if( null === $contentRow ) {
            throw new Exception('content row missing');
          }
        }

        // Get existing parent row (if any)
        $parentContentRow = null;
        if( !empty($parent_id) ) {
          $parentContentRow = $contentRowset->getRowMatching('content_id', $parent_id);
        } else if( !empty($tmp_parent_id) ) {
          $parentContentRow = @$newRowsByTmpId[$tmp_parent_id];
        }

        // Existing row
        if( !empty($contentRow) && is_object($contentRow) ) {
          $existingRowsByContentId[$content_id] = $contentRow;

          // Update row
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          }
          if( empty($contentRow->parent_content_id) ) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }

          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          if( $contentRow->type == 'container' ) {
            $newOrder = array_search($contentRow->name, array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow->order = $newOrder;
          $contentRow->save();
        }

        // New row
        else
        {
          if( empty($element['type']) || empty($element['name']) ) {
            throw new Exception('missing name and/or type info');
          }

          if( $element['type'] == 'container' ) {
            $newOrder = array_search($element['name'], array('top', 'main', 'bottom', 'left', 'right', 'middle')) + 1;
          }

          $contentRow = $contentTable->createRow();
          $contentRow->page_id = $pageObject->page_id;
          $contentRow->order = $newOrder;
          $contentRow->type = $element['type'];
          $contentRow->name = $element['name'];

          // Set parent content
          if( !empty($parentContentRow) ) {
            $contentRow->parent_content_id = $parentContentRow->content_id;
          }
          if( empty($contentRow->parent_content_id) ) {
            $contentRow->parent_content_id = new Zend_Db_Expr('NULL');
          }
          
          // Set params
          if( isset($element['params']) && is_array($element['params']) ) {
            $contentRow->params = $element['params'];
          }

          $contentRow->save();

          $newRowsByTmpId[$tmp_content_id] = $contentRow;
        }
      }

      // Delete rows that were not present in data sent back
      $deletedRowIds = array();
      foreach( $contentRowset as $contentRow ) {
        if( empty($existingRowsByContentId[$contentRow->content_id]) ) {
          $deletedRowIds[] = $contentRow->content_id;
          $contentRow->delete();
        }
      }
      $this->view->deleted = $deletedRowIds;

      // Send back new content info
      $newData = array();
      foreach( $newRowsByTmpId as $tmp_id => $newRow ) {
        $newData[$tmp_id] = $pageTable->createElementParams($newRow);
      }
      $this->view->newIds = $newData;

      $this->view->status = true;
      $this->view->error = false;

      $db->commit();

    } catch( Exception $e ) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = true;
    }
  }
  public function createAction()
  {
    // Get page param
    $page = $this->_getParam('page');
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');

    // Make new page
    if( ($page == 'new' || $page === null) && $this->getRequest()->isPost() ) {
      $pageObject = $pageTable->createRow();
      $pageObject->displayname = ( null !== ($name = $this->_getParam('name')) ? $name : 'Untitled' );
      $pageObject->save();

      // Create a content row for this page
      $contentRow = $contentTable->createRow();
      $contentRow->type = 'container';
      $contentRow->name = 'main';
      $contentRow->page_id = $pageObject->page_id;
      $contentRow->save();

      $contentRow2 = $contentTable->createRow();
      $contentRow2->type = 'container';
      $contentRow2->name = 'middle';
      $contentRow2->page_id = $pageObject->page_id;
      $contentRow2->parent_content_id = $contentRow->content_id;
      $contentRow2->save();
    }

    if( $pageObject ) {
      return $this->_redirectCustom($this->view->url(array('action' => 'index')).'?page='.$pageObject->page_id);
    } else {
      return $this->_redirectCustom($this->view->url(array('action' => 'index')));
    }
  }

  public function saveAction()
  {
    $form = new Core_Form_Admin_Layout_Content_Page();

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $values = $form->getValues();
      $page_id = $values['page_id'];
      unset($values['page_id']);

      if( empty($values['url']) ) {
        $values['url'] = new Zend_Db_Expr('NULL');
      }
      
      $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page_id)->orWhere('page_id = ?', $page_id));

      $pageObject->setFromArray($values)->save();

      $form->addNotice($this->view->translate('Your changes have been saved.'));
    }

    $this->getResponse()->setBody($form->render($this->view));
    $this->_helper->layout->disableLayout(true);
    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }

  public function deleteAction()
  {
    $page_id = $this->_getParam('page');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->error = 'Invalid method';
      return;
    }

    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $page = $pageTable->find($page_id)->current();

    if( null === $page ) {
      $this->view->status = false;
      $this->view->error = 'Page not found';
      return;
    }

    if( !$page->custom ) {
      $this->view->status = false;
      $this->view->error = 'Cannot delete non-custom pages';
      return;
    }

    $pageTable->deletePage($page);

    $this->view->status = true;
  }



  public function widgetAction()
  {
    // Render by widget name
    $mod = $this->_getParam('mod');
    $name = $this->_getParam('name');
    if( null === $name ) {
      throw new Exception('no widget found with name: ' . $name);
    }
    if( null !== $mod ) {
      $name = $mod . '.' . $name;
    }

    $contentInfoRaw = $this->getContentAreas();
    $contentInfo = array();
    foreach( $contentInfoRaw as $info ) {
      $contentInfo[$info['name']] = $info;
    }

    // It has a form specified in content manifest
    if( !empty($contentInfo[$name]['adminForm']) ) {

      if( is_string($contentInfo[$name]['adminForm']) ) { // Core_Form_Admin_Widget_*
        $formClass = $contentInfo[$name]['adminForm'];
        Engine_Loader::loadClass($formClass);
        $this->view->form = $form = new $formClass();
      } else if( is_array($contentInfo[$name]['adminForm']) ) {
        $this->view->form = $form = new Engine_Form($contentInfo[$name]['adminForm']);
      } else {
        throw new Core_Model_Exception('Unable to load admin form class');
      }

      // Try to set title if missing
      if( !$form->getTitle() ) {
        $form->setTitle('Editing: ' . $contentInfo[$name]['title']);
      }

      // Try to set description if missing
      if( !$form->getDescription() ) {
        $form->setDescription('placeholder');
      }

      $form->setAttrib('class', 'global_form_popup ' . $form->getAttrib('class'));

      // Add submit button
      if( !$form->getElement('submit') && !$form->getElement('execute') ) {
        $form->addElement('Button', 'execute', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
        ));
      }

      // Add name
      $form->addElement('Hidden', 'name', array(
        'value' => $name
      ));

      if( !$form->getElement('cancel') ) {
        $form->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => 'parent.Smoothbox.close();',
          'ignore' => true,
          'decorators' => array(
            'ViewHelper',
          ),
        ));
      }

      if( !$form->getDisplayGroup('buttons') ) {
        $submitName = ( $form->getElement('execute') ? 'execute' : 'submit' );
        $form->addDisplayGroup(array(
          $submitName,
          'cancel',
        ), 'buttons', array(
          
        ));
      }

      // Force method and action
      $form->setMethod('post')
        ->setAction($_SERVER['REQUEST_URI']);

      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $this->view->values = $form->getValues();
        $this->view->form = null;
      }
      
      return;
    }

    // Try to render admin page
    if( !empty($contentInfo[$name]) ) {
      try {
        $structure = array(
          'type' => 'widget',
          'name' => $name,
          'request' => $this->getRequest(),
          'action' => 'admin',
          'throwExceptions' => true,
        );

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        $content = $element->render();

        $this->getResponse()->setBody($content);

        $this->_helper->viewRenderer->setNoRender(true);
        return;
      } catch( Exception $e ) {
        
      }
    }

    // Just render default editing form
    $this->view->form = $form = new Engine_Form(array(
      'title' => $contentInfo[$name]['title'],
      'description' => 'placeholder',
      'method' => 'post',
      'action' => $_SERVER['REQUEST_URI'],
      'class' => 'global_form_popup',
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Button',
          'submit',
          array(
            'label' => 'Save',
            'type' => 'submit',
            'decorators' => array('ViewHelper'),
            'ignore' => true,
            'order' => 1501,
          )
        ),
        array(
          'Hidden',
          'name',
          array(
            'value' => $name,
          )
        ),
        array(
          'Cancel',
          'cancel',
          array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'onclick' => 'parent.Smoothbox.close();',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'order' => 1502,
          )
        )
      ),
      'displaygroups' => array(
        'buttons' => array(
          'name' => 'buttons',
          'elements' => array(
            'submit',
            'cancel',
          ),
          'options' => array(
            'order' => 1500,
          )
        )
      )
    ));

    if( !empty($contentInfo[$name]['isPaginated']) ) {
      $form->addElement('Text', 'itemCountPerPage', array(
        'label' => 'Count',
        'description' => 'Number of items to show.',
        'validators' => array(
          array('Int', true),
          array('GreaterThan', true, array(0)),
        )
      ));
    }

    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
      $this->view->values = $form->getValues();
      $this->view->form = null;
    } else {
      $form->populate($this->_getAllParams());
    }
  }






  public function getContentAreas()
  {
    $contentAreas = array();

    // From modules
    $modules = Zend_Controller_Front::getInstance()->getControllerDirectory();
    foreach( $modules as $module => $path ) {
      $contentManifestFile = dirname($path) . '/settings/content.php';
      if( !file_exists($contentManifestFile) ) continue;
      $ret = include $contentManifestFile;
      $contentAreas = array_merge($contentAreas, (array) $ret);
    }

    // From widgets
    $it = new DirectoryIterator(APPLICATION_PATH . '/application/widgets');
    foreach( $it as $dir ) {
      if( !$dir->isDir() || $dir->isDot() ) continue;
      $path = $dir->getPathname();
      $contentManifestFile = $path . '/' . 'manifest.php';
      if( !file_exists($contentManifestFile) ) continue;
      $ret = include $contentManifestFile;
      if( !is_array($ret) ) continue;
      array_push($contentAreas, $ret);
    }

    return $contentAreas;
  }

  public function buildCategorizedContentAreas($contentAreas)
  {
    $categorized = array();
    foreach( $contentAreas as $config ) {
      // Check some stuff
      if( !empty($config['requireItemType']) ) {
        if( is_string($config['requireItemType']) && !Engine_Api::_()->hasItemType($config['requireItemType']) ) {
          $config['disabled'] = true;
        } else if( is_array($config['requireItemType']) ) {
          $tmp = array_map(array(Engine_Api::_(), 'hasItemType'), $config['requireItemType']);
          $config['disabled'] = !(array_sum($tmp) == count($config['requireItemType']));
        }
      }

      // Add to category
      $category = ( isset($config['category']) ? $config['category'] : 'Uncategorized' );
      $categorized[$category][] = $config;
    }

    // Sort categories
    uksort($categorized, array($this, '_sortCategories'));

    // Sort items in categories
    foreach( $categorized as $category => &$items ) {
      usort($items, array($this, '_sortCategoryItems'));
    }

    return $categorized;
  }

  protected function _sortCategories($a, $b)
  {
    if( $a == 'Core' ) return -1;
    if( $b == 'Core' ) return 1;
    return strcmp($a, $b);
  }

  protected function _sortCategoryItems($a, $b)
  {
    if( !empty($a['special']) ) return -1;
    if( !empty($b['special']) ) return 1;
    return strcmp($a['title'], $b['title']);
  }

  protected function _reorderContentStructure($a, $b)
  {
    $sample = array('left', 'middle', 'right');
    $av = $a['name'];
    $bv = $b['name'];
    $ai = array_search($av, $sample);
    $bi = array_search($bv, $sample);
    if( $ai === false && $bi === false ) return 0;
    if( $ai === false ) return -1;
    if( $bi === false ) return 1;
    $r = ( $ai == $bi ? 0 : ($ai < $bi ? -1 : 1) );
    return $r;
  }
}