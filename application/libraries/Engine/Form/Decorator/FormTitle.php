<?php
/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: FormTitle.php 7371 2010-09-14 03:33:35Z john $
 */

/**
 * @category   Engine
 * @package    Engine_Form
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Engine_Form_Decorator_FormTitle extends Zend_Form_Decorator_Abstract
{
    /**
     * Default placement: prepend
     * @var string
     */
    protected $_placement = 'PREPEND';

    /**
     * HTML tag with which to surround label
     * @var string
     */
    protected $_tag;

    /**
     * Set HTML tag with which to surround label
     *
     * @param  string $tag
     * @return Zend_Form_Decorator_Label
     */
    public function setTag($tag)
    {
        if (empty($tag)) {
            $this->_tag = null;
        } else {
            $this->_tag = (string) $tag;
        }
        return $this;
    }

    /**
     * Get HTML tag, if any, with which to surround label
     *
     * @return void
     */
    public function getTag()
    {
        if (null === $this->_tag) {
            $tag = $this->getOption('tag');
            if (null !== $tag) {
                $this->removeOption('tag');
                $this->setTag($tag);
            }
            return $tag;
        }

        return $this->_tag;
    }

    /**
     * Get label to render
     *
     * @return void
     */
    public function getTitle()
    {
        if (null === ($element = $this->getElement())) {
            return '';
        }

        if( method_exists($element, 'getTitle') )
        {
          $label = $element->getTitle();
        }
        else
        {
          $label = $element->getAttrib('title');
          $element->removeAttrib('title');
        }
        $label = trim($label);

        if (empty($label)) {
            return '';
        }

        if (null !== ($translator = $element->getTranslator())) {
            $label = $translator->translate($label);
        }

        return $label;
    }


    /**
     * Render a label
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $label     = $this->getTitle();
        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $tag       = $this->getTag();
        $options   = $this->getOptions();


        if (empty($label) || empty($tag)) {
            return $content;
        }

        //if (!empty($label)) {
            //$options['class'] = $class;
            //$label = $view->formLabel($element->getFullyQualifiedName(), trim($label), $options);
        //} else {
        //    $label = '&nbsp;';
        //}

        if (null !== $tag) {
            require_once 'Zend/Form/Decorator/HtmlTag.php';
            $decorator = new Zend_Form_Decorator_HtmlTag();
            $decorator->setOptions(array('tag' => $tag));

            $label = $decorator->render($label);
        }

        switch ($placement) {
            case self::APPEND:
                return $content . $separator . $label;
            case self::PREPEND:
                return $label . $separator . $content;
        }
    }
}
