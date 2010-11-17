<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * @access	   Sami
 */
?>

<h2><?php echo $this->translate('Delete Event:');?> <?php echo $this->subject()->__toString() ?></h2>


<?php echo $this->form->render($this) ?>