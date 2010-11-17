<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: beta-update.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>


<?php if( empty($this->status) ): ?>

  <div>
    Please run this action if you are using v4.0.0beta3 and are about to upgrade
    to v4.0.0rc1. We will adjust some settings to provide compatibility with the
    manager.
  </div>

  <br />

  <form action="<?php echo $this->url() ?>" method="post">
    <?php echo $this->formButton(null, 'Continue', array('type' => 'submit')) ?>
    or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel</a>
  </form>

<?php elseif( !empty($this->errors) ): ?>

  <div class="error">
    <?php foreach( $this->errors as $error ): ?>
      <?php echo $error ?>
    <?php endforeach; ?>
  </div>

<?php else: ?>

  <div>
    Update complete! You may now use the package manager to upgrade to v4.0.0rc1.
  </div>

  <br />
  <form action="<?php echo $this->url(array('action'=>'select')) ?>" method="post">
    <?php echo $this->formButton(null, 'Continue', array('type' => 'submit')) ?>
  </form>

<?php endif; ?>

