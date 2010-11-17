<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _installMenu.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<h3>
  Install Packages
</h3>

<br />

<p>
  The installer has been upgraded, and requires a restart of the install
  process. The files have already been copied, so if you are asked to choose
  either overwrite or skip, select skip, otherwise continue normally.
</p>

<br />
<br />

<div>
  <?php
    $action = ( empty($this->extractedPackageKeys)
        ? $this->url(array('action' => 'select'))
        : $this->url(array('action' => 'prepare')) );
  ?>
  <form method="post" action="<?php echo $action ?>">
    <button type="submit">Restart</button>
    <?php foreach( (array) $this->extractedPackageKeys as $key ): ?>
      <input type="hidden" name="packages[]" value="<?php echo $key ?>" />
    <?php endforeach; ?>
  </form>
</div>
