<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: place.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<h3>
  Add Packages
</h3>

<?php
  // Navigation
  echo $this->render('_installMenu.tpl')
?>

<br />




<?php // SHOW: Success message ?>
<?php if( !$this->placeError ): ?>
  <p>
    Great! All the files in this installation were copied successfully.
    You can now continue with the installation.
  </p>
<?php endif; ?>


<?php // LIST: Failed uploads ?>
<?php if( $this->settings['verbose'] || $this->placeError ): ?>
  <br />
  <div>
    <?php if( !$this->settings['verbose'] ): ?>
      <ul class="packages_upload_errors">
        <?php foreach( $this->actionSummary as $packageKey => $packageActions ): ?>
          <?php foreach( $packageActions as $packageFile => $packageAction ):
            if( stripos($packageAction, 'failed') === false ) continue;
            ?>
              <li>
                <?php echo $packageFile ?> -
                <?php echo ucfirst($packageAction) ?>
              <li>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <?php foreach( $this->actionSummary as $packageKey => $packageActions ): ?>
        <div>
          <div>
            <?php echo $packageKey ?>
          </div>
          <br />
          <ul class="packages_upload_errors">
            <?php foreach( $packageActions as $packageFile => $packageAction ): ?>
              <li class="<?php echo ( stripos($packageAction, 'failed') === false ? 'success' : 'failed' ) ?>">
                <?php echo $packageFile ?> -
                <?php echo ucfirst($packageAction) ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <br />
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php // CONTINUE || ERROR ?>
<?php if( !$this->placeError ): ?>

  <br />
  <div class="admin_packages_install_submit">
    <?php echo $this->formButton(null, 'Continue', array('onclick' => 'window.location.href = "'.$this->url(array('action' => 'query')).'";')) ?>
    or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
  </div>

<?php else: ?>

  <br />
  <div class="admin_packages_install_error">
    <p>
      Some files failed copying. Please re-apply permissions to their parent directory.
      For example, for application/themes/default/constants.css, apply full permissions recursively (CHMOD -R 0777) to application/themes).
      You may also want to check to make sure your FTP or SSH user has the ability
      to write to files.
    </p>
    <?php if( $this->settings['force'] ): ?>
      <div class="admin_packages_install_submit">
        <?php echo $this->formButton(null, 'Continue Anyway', array('onclick' => 'if( confirm("Are you sure?") ) { window.location.href = "'.$this->url(array('action' => 'query')).'"; }')) ?>
        or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
      </div>
    <?php endif; ?>
  </div>

<?php endif; ?>
