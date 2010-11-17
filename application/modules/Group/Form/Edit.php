<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7517 2010-10-01 09:18:15Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Group_Form_Edit extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $this
      ->setTitle('Edit Group');

    $this->addElement('Text', 'title', array(
      'label' => 'Group Name',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
	//        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('Textarea', 'description', array(
      'label' => 'Description',
      'validators' => array(
        array('NotEmpty', true),
        //array('StringLength', false, array(1, 1027)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => 1027)),
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    $this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => array(
        '0' => ' '
      ),
    ));

    $this->addElement('Radio', 'search', array(
      'label' => 'Include in search results?',
      'multiOptions' => array(
        '1' => 'Yes, include in search results.',
        '0' => 'No, hide from search results.',
      ),
      'value' => '1',
    ));

    $this->addElement('Radio', 'auth_invite', array(
      'label' => 'Let members invite others?',
      'multiOptions' => array(
        'member' => 'Yes, members can invite other people.',
        'officer' => 'No, only officers can invite other people.',
      ),
      'value' => 'member',
    ));

    $this->addElement('Radio', 'approval', array(
      'label' => 'Approve members?',
      'description' => ' When people try to join this group, should they be allowed '.
        'to join immediately, or should they be forced to wait for approval?',
      'multiOptions' => array(
        '0' => 'New members can join immediately.',
        '1' => 'New members must be approved.',
      ),
      'value' => '0',
    ));

    // Privacy
    $availableLabels = array(
      'everyone'    => 'Everyone',
      'registered'  => 'Registered Members',
      'member'      => 'All Group Members',
      'officer'     => 'Officers and Owner Only',
      'owner'       => 'Owner Only',
    );


    // View
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
    
    if( !empty($viewOptions) && count($viewOptions) > 1 ) {
      $this->addElement('Select', 'auth_view', array(
        'label' => 'View Privacy',
        'description' => 'Who may see this group?',
        'multiOptions' => $viewOptions,
        'value' => key($viewOptions),
      ));
      $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    // Comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));
    
    if( !empty($commentOptions) && count($commentOptions) > 1 ) {
      $this->addElement('Select', 'auth_comment', array(
        'label' => 'Comment Privacy',
        'description' => 'Who may post on this group\'s wall?',
        'multiOptions' => $commentOptions,
        'value' => key($commentOptions),
      ));
      $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
    }

    // Photo
    $photoOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_photo');
    $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));
    
    if( !empty($photoOptions) && count($photoOptions) > 1 ) {
      $this->addElement('Select', 'auth_photo', array(
        'label' => 'Photo Uploads',
        'description' => 'Who may upload photos to this group?',
        'multiOptions' => $photoOptions,
        'value' => key($photoOptions),
      ));
      $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
    }



    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}