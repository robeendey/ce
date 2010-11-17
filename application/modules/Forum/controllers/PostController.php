<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PostController.php 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Forum_PostController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( 0 !== ($post_id = (int) $this->_getParam('post_id')) &&
        null !== ($post = Engine_Api::_()->getItem('forum_post', $post_id)) &&
        $post instanceof Forum_Model_Post ) {
      Engine_Api::_()->core()->setSubject($post);
    }
  }

  public function deleteAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject('forum_post')->isValid() ) {
      return;
    }
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('forum_post');
    $this->view->topic = $topic = $post->getParent();
    $this->view->forum = $forum = $topic->getParent();
    if( !$this->_helper->requireAuth()->setAuthParams($post, null, 'delete')->checkRequire() &&
        !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.delete')->checkRequire() ) {
      return $this->_helper->requireAuth()->forward();
    }
    
    $this->view->form = $form = new Forum_Form_Post_Delete();
    
    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('forum_post');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $post->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    $href = ( null === $topic ? $forum->getHref() : $topic->getHref() );
    return $this->_forward('success', 'utility', 'core', array(
      'closeSmoothbox' => true,
      'parentRedirect' => $href,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
      'format' => 'smoothbox'
    ));
  }

  public function editAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject('forum_post')->isValid() ) {
      return;
    }
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->post = $post = Engine_Api::_()->core()->getSubject('forum_post');
    $this->view->topic = $topic = $post->getParent();
    $this->view->forum = $forum = $topic->getParent();
    if( !$this->_helper->requireAuth()->setAuthParams($post, null, 'edit')->checkRequire() &&
        !$this->_helper->requireAuth()->setAuthParams($forum, null, 'topic.edit')->checkRequire() ) {
      return $this->_helper->requireAuth()->forward();
    }

    $this->view->form = $form = new Forum_Form_Post_Edit(array('post'=>$post));
    $form->body->setValue($post->body);
    $form->photo->setValue($post->file_id);   

    if( !$this->getRequest()->isPost() ) {
      return;
    }
    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('forum_post');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();

      $post->body = $values['body'];
      $post->edit_id = $viewer->getIdentity();

      //DELETE photo here.
      if( !empty($values['photo_delete']) && $values['photo_delete'] ) {
        $post->deletePhoto();
      }

      if( !empty($values['photo']) ) {
        $post->setPhoto($form->photo);
      }

      $post->save();

      $db->commit();

      return $this->_helper->redirector->gotoRoute(array('post_id'=>$post->getIdentity(), 'topic_id' => $post->getParent()->getIdentity()), 'forum_topic', true);
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
}