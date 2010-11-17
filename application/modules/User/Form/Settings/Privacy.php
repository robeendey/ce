<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Privacy.php 7486 2010-09-28 03:00:23Z john $
 * @author     Steve
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class User_Form_Settings_Privacy extends Engine_Form
{
  public    $saveSuccessful  = FALSE;
  protected $_roles           = array('owner', 'member', 'network', 'registered', 'everyone');
  protected $_item;

  public function setItem(User_Model_User $item)
  {
    $this->_item = $item;
  }

  public function getItem()
  {
    if( null === $this->_item ) {
      throw new User_Model_Exception('No item set in ' . get_class($this));
    }

    return $this->_item;
  }
  
  public function init()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = $this->getItem();


    $this->setTitle('Privacy Settings')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;

    // Init blocklist
    $this->addElement('Hidden', 'blockList', array(
      'label' => 'Blocked Members',
      'description' => 'Adding a person to your block list makes your profile (and all of your other content) unviewable to them. Any connections you have to the blocked person will be canceled. To add someone to your block list, visit that person\'s profile page.',
      'order' => -1
    ));
    Engine_Form::addDefaultDecorators($this->blockList);
    
    // Init search
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Do not display me in searches, browsing members, or the "Online Members" list.',
      'checkedValue' => 0,
      'uncheckedValue' => 1,
    ));

    // Init showprofileviews
    /*
    $this->addElement('Checkbox', 'show_profileviewers', array(
      'label' => 'Yes, display users who viewed my profile.',
      //'description' => 'Show Profile Views',
    ));
    */
    
    $availableLabels = array(
      'owner'       => 'Only Me',
      'member'      => 'Only My Friends',
      'network'     => 'Friends & Networks',
      'registered'  => 'All Registered Members',
      'everyone'    => 'Everyone',
    );
    
    // Init profile view
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_view');
    $view_options = array_intersect_key($availableLabels, array_flip($view_options));

    $this->addElement('Radio', 'privacy', array(
      'label' => 'Profile Privacy',
      'description' => 'Who can view your profile?',
      'multiOptions' => $view_options,
    ));

    foreach( $this->_roles as $role ) {
      if( 1 === $auth->isAllowed($user, $role, 'view') ) {
        $this->privacy->setValue($role);
      }
    }

    // Init profile comment
    $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_comment');
    $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
    
    $this->addElement('Radio', 'comment', array(
      'label' => 'Profile Posting Privacy',
      'description' => 'Who can post on your profile?',
      'multiOptions' => $comment_options,
    ));
    
    foreach( $this->_roles as $role ) {
      if( 1 === $auth->isAllowed($user, $role, 'comment') ) {
        $this->comment->setValue($role);
      }
    }

    // Init publishtypes
    if( Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.publish', true) ) {
      $this->addElement('MultiCheckbox', 'publishTypes', array(
        'label' => 'Recent Activity Privacy',
        'description' => 'Which of the following things do you want to have published about you in the recent activity feed? Note that changing this setting will only affect future news feed items.',
      ));
    }

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
    
    return $this;
  }

  public function save()
  {
    $auth = Engine_Api::_()->authorization()->context;
    $user = $this->getItem();

    $privacy_max_role = array_search($this->getValue('privacy'), $this->_roles);
    foreach( $this->_roles as $i => $role )
      $auth->setAllowed($user, $role, 'view', ($i <= $privacy_max_role) );

    $comment_max_role = array_search($this->getValue('comment'), $this->_roles);
    foreach( $this->_roles as $i => $role )
      $auth->setAllowed($user, $role, 'comment', ($i <= $comment_max_role) );
  }
} // end public function save()