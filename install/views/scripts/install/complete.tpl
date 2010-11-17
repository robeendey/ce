<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: complete.tpl 7383 2010-09-14 22:28:24Z john $
 * @author     John
 */
?>

<?php $this->headTitle($this->translate('Step %1$s', 5))->headTitle($this->translate('Complete')) ?>

<h1>
  <?php echo $this->translate('Congratulations! You\'re ready to go.') ?>
</h1>

<p>
  <?php echo $this->translate('We\'ve successfully completed the installation process.') ?>

  <?php
    $appBaseHref = str_replace('install/', '', $this->url(array(), 'default', true));
    $url = $appBaseHref . 'admin/';
  ?>
  <?php echo $this->translate(' You can now sign in to your %1$s and get started.',
    $this->htmlLink($url, $this->translate('control panel'))) ?>
</p>

<p>
  <?php echo $this->translate('Thanks again for choosing SocialEngine. We hope you enjoy using it as much as we enjoyed making it!') ?>
</p>

<br />

<p>
  <?php echo $this->translate('Love,') ?>
</p>
<p class="love">
  <?php echo $this->translate('The SE Team') ?>
</p>

<?php if( !empty($this->form) ): ?>

  <?php echo $this->form->render($this) ?>

<?php endif; ?>