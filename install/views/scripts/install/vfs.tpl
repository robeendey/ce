<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: vfs.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php $this->headTitle($this->translate('Step %1$s', 2.1))->headTitle($this->translate('FTP Info')) ?>

<h1>
  <?php echo $this->translate('Step 2: Check Requirements (set permissions)') ?>
</h1>

<p>
  <?php echo $this->translate('To attempt to set permissions on your server automatically, please provide your FTP information here. If you would prefer to do this manually, you can return to the list of requirements to review the permissions that need changing.') ?>
</p>

<br />

<?php echo $this->form->render($this) ?>