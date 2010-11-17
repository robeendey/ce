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

<h3>Add Packages</h3>

<?php
  // Navigation
  echo $this->render('_installMenu.tpl')
?>

<br />


<?php if( $this->form ): ?>

  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <form action="<?php echo $this->url() ?>">
    <?php echo $this->formRadio('location', null, array(), $this->paths) ?>
    <br />
    <br />
    <?php echo $this->formButton('submit', 'Select Path', array('type' => 'submit')) ?>

  </form>

<?php endif; ?>