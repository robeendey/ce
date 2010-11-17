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

<h2>
  Conflicting Packages
</h2>

<p>

</p>

<br />


<?php if( empty($this->conflicts) ): ?>

  <div class="ok">
    There are no packages with conflicting files.
  </div>

<?php else: ?>

  <ul>
    <?php foreach( $this->conflicts as $file => $fileConflicts ): ?>
      <li>
        <h4>
          <?php echo $file ?>
        </h4>
        <ul>
          <?php foreach( $fileConflicts as $conflictPackageKey ): ?>
            <li>
              <?php echo $conflictPackageKey ?>
            </li>
          <?php endforeach; ?>
        </ul>
        <br />
      </li>
    <?php endforeach; ?>
  </ul>

<?php endif; ?>
