<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * @author		 John
 */
?>

<h2><?php echo $this->translate('Delete Group:')?> <?php echo $this->subject()->__toString() ?></h2>

<div class='tabs'>
  <?php
    echo $this->navigation()
      ->menu()
      ->setContainer($this->navigation)
      ->render()
  ?>
</div>


<?php echo $this->form->render($this) ?>