<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: query.tpl 7244 2010-09-01 01:49:53Z john $
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




<?php // SHOW: Success message ?>
<?php if( !$this->queryError ): ?>
  <p>
    Excellent! All of the necessary database changes have been made successfully.
    We can now finalize the installation.
  </p>
  <br />
<?php endif; ?>


<?php if( !empty($this->results) ): ?>
  <?php foreach( $this->results as $packageKey => $results ): ?>
    <div class="package_query_results">
      <span class="package_query_title">
        <?php if( isset($this->packageTitles[$packageKey]) ): ?>
          <?php echo $this->packageTitles[$packageKey] ?>
        <?php else: ?>
          <?php echo $packageKey; //$result['operation']->getPackage()->getKey() ?>
        <?php endif; ?>
      </span>
      <ul>
        <?php foreach( $results as $result ): ?>
          <?php foreach( $result['errors'] as $error ): ?>
            <li class="error">
              <?php echo $error ?>
            </li>
          <?php endforeach; ?>
          <?php foreach( $result['messages'] as $error ): ?>
            <li class="message">
              <?php echo $error ?>
            </li>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endforeach; ?>
  <br />
<?php endif; ?>



<?php // LIST: pre-install messages (verbose only) ?>
<?php /* if( $this->settings['verbose'] ): ?>
  <?php foreach( $this->preResults as $result ): ?>
    <div>
      <span>
        <?php echo $result['operation']->getPackage()->getKey() ?>
      </span>
      <span>
        <?php echo $result['error'] ? 'error' : 'okay' ?>
      </span>
    </div>
  <?php endforeach; ?>
<?php endif; */ ?>


<?php // CONTINUE || ERROR ?>
<?php if( !$this->queryError ): ?>

  <div class="admin_packages_install_submit">
    <?php echo $this->formButton(null, 'Finalize Installation', array('onclick' => 'window.location.href = "'.$this->url(array('action' => 'complete')).'";')) ?>
    or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
  </div>

<?php else: ?>

  <div class="admin_packages_install_error">
    <div class="tip">
      <span>
        Warning: Some of the database queries were not completed successfully.
      </span>
    </div>
    <?php if( $this->settings['force'] ): ?>
      <div class="admin_packages_install_submit">
        <?php echo $this->formButton(null, 'Finalize Anyway', array('onclick' => 'if( confirm("Are you sure?") ) { window.location.href = "'.$this->url(array('action' => 'complete')).'"; }')) ?>
        or <a href="<?php echo $this->url(array('action' => 'index')) ?>">cancel installation</a>
      </div>
    <?php endif; ?>
  </div>

<?php endif; ?>
