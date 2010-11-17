<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Page.php 7453 2010-09-23 03:59:38Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Core_Form_Admin_Layout_Content_Page extends Engine_Form
{
  public function init()
  {
    $this
      ->setMethod('post')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'save', 'controller' => 'content', 'module' => 'core'), 'admin_default', true))
      ->setAttrib('class', 'admin_layoutbox_menu_editinfo_form')
      ->setAttrib('id', 'admin_content_pageinfo')
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('HtmlTag', array('tag' => 'ul'))
      ->addDecorator('FormErrors', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('FormMessages', array('placement' => 'PREPEND', 'escape' => false))
      ->addDecorator('Form')
      ;

    $this->addElement('Text', 'displayname', array(
      'label' => 'Page Name <span>(for your reference only)</span>',
      'decorators' => array(
        array('ViewHelper'),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Text', 'title', array(
      'label' => 'Page Title <span>(title tag)</span>',
      'decorators' => array(
        array('ViewHelper'),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Text', 'url', array(
      'label' => 'Page URL',
      'description' => 'The URL may only contain alphanumeric characters and dashes - any other characters will be stripped. The full url will be http://www.yoursite.com/pages/[url]',
      'filters' => array(
        array('PregReplace', array('/[^a-z0-9]+|[-]{2,}/i', '-')),
        array('StringTrim', array("- \n\r\t"))
      ),
      'decorators' => array(
        array('ViewHelper'),
        array('Description', array('escape' => false, 'placement' => 'append')),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Text', 'description', array(
      'label' => 'Page Description <span>(meta tag)</span>',
      'decorators' => array(
        array('ViewHelper'),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Text', 'keywords', array(
      'label' => 'Page Keywords <span>(meta tag)</span>',
      'allowEmpty' => false,
      'validators' => array(
        
      ),
      'decorators' => array(
        array('ViewHelper'),
        array('Label', array('tag' => 'span', 'escape' => false)),
        array('HtmlTag', array('tag' => 'li')),
      ),
    ));

    $this->addElement('Hidden', 'page_id', array(
      'validators' => array(
        array('NotEmpty'),
        array('Int'),
      ),
    ));
    
  }
}