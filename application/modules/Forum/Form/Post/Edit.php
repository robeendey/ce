<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Forum_Form_Post_Edit extends Engine_Form
{
  public $_error = array(); 
  protected $_post;

  public function setPost($post)
  {
    $this->_post = $post;
 
  }

  public function init()
  {   
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->setMethod("POST");
    $this->setAttrib('name', 'forum_post_edit');
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
 
    $filter = new Engine_Filter_Html();
    $viewer = Engine_Api::_()->user()->getViewer();
    $allowed_tags = explode(',', Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'forum', 'commentHtml'));

    if ($settings->getSetting('forum_html', 0) == '0')
    {
      $filter->setForbiddenTags();
      $filter->setAllowedTags($allowed_tags);

    }

    if (($settings->getSetting('forum_html',0) == '1') || ($settings->getSetting('forum_bbcode',0) == '1')) {
    $this->addElement('TinyMce', 'body', array(
      'disableLoadDefaultDecorators' => true,
      'required' => true,
      'editorOptions' => array(
        'bbcode' => $settings->getSetting('forum_bbcode', 0),
        'html' => $settings->getSetting('forum_html', 0)
      ),
      'allowEmpty' => false,
      'decorators' => array('ViewHelper'),
      'filters' => array(
        $filter,
        new Engine_Filter_Censor(),
      ),
    ));
    }
    else
      {
    $this->addElement('textarea', 'body', array(
      'required' => true,
      'attribs' => array('rows'=>24, 'cols'=>80, 'style'=>'width:553px; max-width:553px;height:158px;'),

      'allowEmpty' => false,
      'filters' => array(
        $filter,
        new Engine_Filter_Censor(),
      ),
    ));
      }
    if (!empty($this->_post->file_id)) 
    {
      $photo_delete_element = new Engine_Form_Element_Checkbox('photo_delete', array('label'=>'This post has a photo attached. Do you want to delete it?'));
      $photo_delete_element->setAttrib('onchange', 'updateUploader()');
      $this->addElement($photo_delete_element);
      $this->addDisplayGroup(array('photo_delete'), 'photo_delete_group');    
    }

    // Photo
    $file_element = new Engine_Form_Element_File('photo', array(
      'label' => 'Attach a New Photo (optional)',
      'size' => '40'
    ));
    $this->addElement($file_element);
    $this->addDisplayGroup(array('photo'), 'photo_group');    
   if (!empty($this->_post->file_id)) 
    {
      $this->getDisplayGroup('photo_group')->getDecorator('HtmlTag')->setOption('style', 'display:none;');
    }

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
    $button_group->addDecorator('DivDivDivWrapper');
  }



}