<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: perms.tpl 7607 2010-10-08 00:23:49Z john $
 * @author     John
 */
?>

<h3>
  Install Packages
</h3>

<?php
  // Navigation
  echo $this->render('_installMenu.tpl')
?>

<br />

<p>
  We found your community's installation directory in this path:
  <?php echo $this->vfsPath ?>
</p>

<br />

<p>
  <?php echo $this->operationCount ?> files will be copied during this installation.
</p>

<?php // SHOW: Success message ?>
<?php if( $this->notWritableCount <= 0 ): ?>
  <br />
  <div>
    We've completed the permissions check and all the necessary permissions have been set.
    You can now continue with the installation.
  </div>
<?php endif; ?>

<?php // LIST: Failed perm checks ?>
<?php if( $this->settings['verbose'] || $this->notWritableCount > 0 ): ?>

  <br />
  <h3>Permission issues found:</h3>
  <div>
    <?php if( !$this->settings['verbose'] ): ?>
      <ul class="packages_perm_errors">
        <?php foreach( $this->permResults as $packageKey => $packageFiles ): ?>
          <?php foreach( $packageFiles as $packageFile => $writable ): ?>
            <?php if( !$writable ): ?>
              <li class="<?php echo ( $writable ? 'writeable' : 'not-writeable' ) ?>">
                <?php echo $packageFile ?>
                is <?php if( !$writable): ?>not <?php endif; ?>writable.
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <?php foreach( $this->permResults as $packageKey => $packageFiles ): ?>
        <div>
          <?php echo $packageKey ?>
          (<?php echo sprintf('writable: %d, not writable %d', $this->permSummary[$packageKey]['writable'], $this->permSummary[$packageKey]['not-writable']) ?>)
        </div>
        <ul class="packages_perm_errors">
          <?php foreach( $packageFiles as $packageFile => $writable ): ?>
            <?php if( $this->settings['verbose'] ): ?>
              <li class="<?php echo ( $writable ? 'writeable' : 'not-writeable' ) ?>">
                <?php echo $packageFile ?>
                is <?php if( !$writable ): ?>not <?php endif; ?>writable.
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      <?php endforeach; ?>
    <?php endif; ?>

    <br/>
  </div>

<?php endif; ?>




<?php // CONTINUE || ERROR ?>
<?php if( $this->notWritableCount <= 0 ): ?>

  <br />
  <div class="admin_packages_install_submit">
    <?php echo $this->formButton(null, 'Continue', array('onclick' => 'window.location.href = "'.$this->url(array('action' => 'place')).'";')) ?>
    or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
  </div>

<?php else: ?>

  <div class="admin_packages_install_error">
    <div class="tip">
      <span>
        Please resolve the permission issues listed above, and then click the "Refresh" button below to try again.
      </span>
    </div>
      <div class="admin_packages_install_submit">
        <button onClick="location.reload(true);">Refresh</button>
        <?php if( $this->settings['force'] ): ?>
          <?php echo $this->formButton(null, 'Continue Anyway', array('onclick' => 'if( confirm("Are you sure?") ) { window.location.href = "'.$this->url(array('action' => 'place')).'"; }')) ?>
        <?php endif; ?>
        or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
      </div>
  </div>

<?php endif; ?>
