<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: default-simple.tpl 7443 2010-09-22 07:25:41Z john $
 * @author     John
 */
?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html id="smoothbox_window" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->hooks('onRenderLayoutDefaultSimple', $this) ?>


  <?php // TITLE/META ?>
  <?php
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $this
      //->headTitle($request->getActionName(), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      //->headTitle($request->getControllerName(), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      //->headTitle($request->getModuleName(), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ->headTitle($this->translate("_SITE_TITLE"), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', 'en-US');
    if( $this->subject() && $this->subject()->getIdentity() ) {
      //$this->headTitle($this->subject()->getTitle());
      //$this->headMeta()->appendName('description', $this->subject()->getDescription());
      //$this->headMeta()->appendName('keywords', $this->subject()->getKeywords());
    }
  ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>


  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => '/favicon.ico',
      'type' => 'image/x-icon'),
      'PREPEND');
    $themes = array();
    if( !empty($this->layout()->themes) ) {
      $themes = $this->layout()->themes;
    } else {
      $themes = array('default');
    }
    foreach( $themes as $theme ) {
      $this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/themes/'.$theme.'/theme.css');
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>
  
  <?php // TRANSLATE ?>
  <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

  <?php // SCRIPTS ?>
  <script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');

    Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');

    en4.core.loader = new Element('img', {src: 'application/modules/Core/externals/images/loading.gif'});

    en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');

    <?php if( $this->subject() ): ?>
      en4.core.subject = {
        type : '<?php echo $this->subject()->getType(); ?>',
        id : <?php echo $this->subject()->getIdentity(); ?>,
        guid : '<?php echo $this->subject()->getGuid(); ?>'
      };
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
      en4.user.viewer = {
        type : '<?php echo $this->viewer()->getType(); ?>',
        id : <?php echo $this->viewer()->getIdentity(); ?>,
        guid : '<?php echo $this->viewer()->getGuid(); ?>'
      };
    <?php endif; ?>
    if( <?php echo ( Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false' ) ?> ) {
      en4.core.dloader.attach();
    }
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
  </script>
  <?php
    $this->headScript()
      ->prependFile($this->baseUrl().'/externals/smoothbox/smoothbox4.js')
      ->prependFile($this->baseUrl().'/application/modules/User/externals/scripts/core.js')
      ->prependFile($this->baseUrl().'/application/modules/Core/externals/scripts/core.js')
      ->prependFile($this->baseUrl().'/externals/chootools/chootools.js')
      ->prependFile($this->baseUrl().'/externals/mootools/mootools-1.2.4.4-more-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($this->baseUrl().'/externals/mootools/mootools-1.2.4-core-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js');
  ?>
  <?php echo $this->headScript()->toString()."\n" ?>

  <!-- vertical scrollbar fix -->
  <style type="text/css">
    html, body
    {
      overflow-y: auto;
      margin: 0px;
    }
  </style>


</head>
<body id="global_page_<?php echo $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName() ?>">
  <span id="global_content_simple">
    <?php echo $this->layout()->content ?>
  </span>
</body>
</html>