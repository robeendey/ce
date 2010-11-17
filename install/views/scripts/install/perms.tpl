<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: perms.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php $this->headTitle($this->translate('Step %1$s', 2.2))->headTitle($this->translate('Permissions')) ?>

<h1>
  <?php echo $this->translate('Step 2: Check Requirements (set permissions)') ?>
</h1>

<?php if( !empty($this->errors) ): ?>

  <div class="error">
    <p>
      The necessary permissions could not be applied to the following folders.
    </p>
    <br />
    <?php foreach( $this->errors as $error ): ?>
      <div>
        <?php echo $error ?><br />
      </div>
    <?php endforeach; ?>
  </div>

  <br />

  <button onclick="window.location.href = '<?php echo $this->url(array('action' => 'sanity')); ?>'; return false;">
    <?php echo $this->translate('Retry Requirements Test Anyway') ?>
  </button>

<?php else: ?>

  <div class="ok">
    <?php echo $this->translate('The necessary permissions have been applied successfully! You can now return to the requirements list.') ?>
  </div>

  <br />

  <button onclick="window.location.href = '<?php echo $this->url(array('action' => 'sanity')); ?>'; return false;">
    <?php echo $this->translate('Retry Requirements Test') ?>
  </button>

<?php endif; ?>