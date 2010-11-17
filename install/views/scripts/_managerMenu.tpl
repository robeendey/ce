<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _managerMenu.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php if( isset($this->layout()->mainNavigation) ): ?>
  <div class="tabs packagemanager">
    <?php
      echo $this->navigation()
        ->menu()
        ->setContainer($this->layout()->mainNavigation)
        ->render();
    ?>
  </div>
<?php endif; ?>