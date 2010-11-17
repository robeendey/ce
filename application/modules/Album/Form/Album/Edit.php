<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7486 2010-09-28 03:00:23Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Album_Form_Album_Edit extends Engine_Form
{
  public function init()
  {
    $user = Engine_Api::_()->user()->getViewer();

    $user_level = Engine_Api::_()->user()->getViewer()->level_id;
    $this->setTitle('Edit Album Settings')
      ->setAttrib('name', 'albums_edit');
    
    $this->addElement('Text', 'title', array(
      'label' => 'Album Title',
      'required' => true,
      'notEmpty' => true,
      'validators' => array(
        'NotEmpty',
      ),
      'filters' => array(
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
      )
    ));
    $this->title->getValidator('NotEmpty')->setMessage("Please specify an album title");
    
    // prepare categories
    $categories = Engine_Api::_()->album()->getCategories();
    if (count($categories)!=0){
      $categories_prepared[0]= "";
      foreach ($categories as $category){
        $categories_prepared[$category->category_id]= $category->category_name;
      }

      // category field
      $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $categories_prepared
          ));
    }

    $this->addElement('Textarea', 'description', array(
      'label' => 'Album Description',
      'rows' => 2,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      )
    ));

    $this->addElement('Checkbox', 'search', array(
      'label' => "Show this album in search results",
    ));

    // View
    $availableLabels = array(
      'everyone'            => 'Everyone',
      'registered'          => 'All Registered Members',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me'
    );

    // Element: auth_view
    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    if( count($viewOptions) >= 1 ) {
      $this->addElement('Select', 'auth_view', array(
        'label' => 'Privacy',
        'description' => 'Who may see this album?',
        'multiOptions' => $viewOptions,
        'value' => key($viewOptions),
      ));
    }

    // Element: auth_comment
    $commentOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    if( count($commentOptions) >= 1 ) {
      $this->addElement('Select', 'auth_comment', array(
        'label' => 'Comment Privacy',
        'description' => 'Who may post comments on this album?',
        'multiOptions' => $commentOptions,
        'value' => key($commentOptions),
      ));
    }

    // Element: auth_tag
    $tagOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('album', $user, 'auth_tag');
    $tagOptions = array_intersect_key($availableLabels, array_flip($tagOptions));

    if( count($tagOptions) >= 1 ) {
      $this->addElement('Select', 'auth_tag', array(
        'label' => 'Tagging',
        'description' => 'Who may tag photos in this album?',
        'multiOptions' => $tagOptions,
        'value' => key($tagOptions),
      ));
    }
    

    // Submit or succumb!
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Album',
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper'
      )
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
  }
}
