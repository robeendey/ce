<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: LinkController.php 7423 2010-09-20 03:24:33Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_LinkController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->contextSwitch
      ->addActionContext('create', 'json')
      ->addActionContext('preview', 'json')
      ->initContext();
  }
  
  public function indexAction()
  {
    $key = $this->_getParam('key');
    $uri = $this->_getParam('uri');
    $link = Engine_Api::_()->getItem('core_link',  $this->_getParam('id', $this->_getParam('link_id')));
    Engine_Api::_()->core()->setSubject($link);

    if( !$this->_helper->requireSubject()->isValid() ) return;
    //if( !$this->_helper->requireAuth()->setAuthParams($link, null, 'view')->isValid() ) return;

    if( $key != $link->getKey() )
    {
      throw new Exception('whoops');
    }
    
    $link->view_count++;
    $link->save();

    $this->_helper->viewRenderer->setNoRender(true);
    $this->_helper->redirector->gotoUrl($link->uri);
  }

  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid() ) return;
    
    // Make form
    $this->view->form = $form = new Core_Form_Link_Create();
    $translate        = Zend_Registry::get('Zend_Translate');

    // Check method
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = $translate->_('Invalid method');
      return;
    }

    // Check data
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = $translate->_('Invalid data');
    }

    // Process
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $table = Engine_Api::_()->getDbtable('links', 'core');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $link = Engine_Api::_()->getApi('links', 'core')->createLink($viewer, $form->getValues());
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    
    $this->view->status   = true;
    $this->view->message  = $translate->_('Link created');
    $this->view->identity = $link->getIdentity();
  }

  public function deleteAction(){
    $viewer = Engine_Api::_()->user()->getViewer();
    $link = Engine_Api::_()->getItem('core_link', $this->getRequest()->getParam('link_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($link, null, 'delete')->isValid()) return;

    $this->view->form = $form = new Core_Form_Link_Delete();
    $translate        = Zend_Registry::get('Zend_Translate');

    if( !$link )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Link doesn't exists or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $link->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $link->delete();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Link has been deleted.');
    return $this->_forward('success' ,'utility', 'core', array(
      'parentRefresh' => true,
      'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Link has been deleted.'))
    ));
  }

  public function previewAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid() ) return;

    // clean URL for html code
    $uri = strip_tags($this->_getParam('uri'));
    //$uri = $this->_getParam('uri');
    $info = parse_url($uri);
    $this->view->url = $uri;
    
    try
    {
      $client = new Zend_Http_Client($uri, array(
        'maxredirects' => 2,
        'timeout'      => 10,
      ));

      // Try to mimic the requesting user's UA
      $client->setHeaders(array(
        'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
        'X-Powered-By' => 'Zend Framework'
      ));

      $response = $client->request();
      $body = $response->getBody();
      if( function_exists('mb_convert_encoding') ) {
        $body = mb_convert_encoding($body, 'HTML-ENTITIES', "UTF-8");
      }

      if( class_exists('DOMDocument') ) {
        $dom = new Zend_Dom_Query($body);
      } else {
        $dom = null; // Maybe add b/c later
      }

      $title = null;
      if( $dom ) {
        $titleList = $dom->query('title');
        if( count($titleList) > 0 ) {
          $title = $titleList->current()->textContent;
          $title = substr($title, 0, 255);
        }
      }
      $this->view->title = $title;

      $description = null;
      if( $dom ) {
        $descriptionList = $dom->queryXpath("//meta[@name='description']");
        if( count($descriptionList) > 0 ) {
          $description = $descriptionList->current()->getAttribute('content');
          $description = substr($description, 0, 255);
        }
      }
      $this->view->description = $description;

      // Get baseUrl and baseHref to parse . paths
      $baseUrlInfo = parse_url($uri);
      $baseUrl = null;
      if( $dom ) {
        $baseUrlList = $dom->query('base');
        if( $baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href') ) {
          $baseUrl = $baseUrlList->current()->getAttribute('href');
        }
      }
      if( !$baseUrl ) {
        if( empty($baseUrlInfo['path']) ) {
          $baseUrl = $baseUrlInfo['scheme'].'://'.$baseUrlInfo['host'].'/';
        } else {
          $baseUrl = explode('/', $baseUrlInfo['path']);
          array_pop($baseUrl);
          $baseUrl = join('/', $baseUrl);
          $baseUrl = trim($baseUrl, '/');
          $baseUrl = $baseUrlInfo['scheme'].'://'.$baseUrlInfo['host'].'/'.$baseUrl.'/';
        }
      }

      $images = array();
      if( $dom ) {
        $imageQuery = $dom->query('img');
        foreach( $imageQuery as $image )
        {
          $src = $image->getAttribute('src');
          // Ignore images that don't have a src
          if( !$src || false === ($srcInfo = @parse_url($src)) )
          {
            //$debug[$src] = 'Could not parse url';
            continue;
          }
          // If relative to root, add host
          if( strpos($src, '/') === 0 )
          {
            $src = $info['scheme'].'://'.$info['host'].$src;
          }
          // If relative to current path, add baseUrl
          if( strpos($src, './') === 0 )
          {
            $src = $baseUrl . substr($src, 2);
          }
          // Ignore images that don't come from the same domain
          if( strpos($src, $info['host']) === false )
          {
            // @todo should we do this? disabled for now
            //continue;
          }
          // Ignore images that don't end in an image extension
          $ext = ltrim(strrchr($src, '.'), '.');
          if( !in_array($ext, array('jpg', 'jpeg', 'gif', 'png')) )
          {
            // @todo should we do this? disabled for now
            //continue;
          }
          $images[] = $src;
        }
      }

      // Unique
      $images = array_values(array_unique($images));

      // Truncate if greater than 20
      if( count($images) > 30 ) {
        array_splice($images, 30, count($images));
      }

      $this->view->imageCount = count($images);
      $this->view->images = $images;
    }

    catch( Exception $e )
    {
      throw $e;
      //$this->view->title = $uri;
      //$this->view->description = $uri;
      //$this->view->images = array();
      //$this->view->imageCount = 0;
    }
  }
}