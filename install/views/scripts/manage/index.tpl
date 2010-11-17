<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<h3>
  Manage Packages
</h3>

<p>
  Packages are plugins, themes, mods, and other extensions that you can add
  to your social network. 
</p>

<br />

<div>
  <a class="buttonlink admin_packages_add" href="<?php echo $this->url(array('action' => 'select')) ?>">Install New Packages</a>
</div>

<br />

<?php if( !empty($this->installedPackages) ): ?>
  <ul class="admin_packages">
    <?php foreach( $this->installedPackages as $packageInfo ):
      $package = $packageInfo['package'];
      $upgradeable = $packageInfo['upgradeable'];
      $upgrade_version = null;
      if( isset($this->remoteVersions[$package->getGuid()]) && version_compare($this->remoteVersions[$package->getGuid()]['version'], $package->getVersion(), '>') ) {
        $upgradeable = true;
        $upgrade_version = $this->remoteVersions[$package->getGuid()]['version'];
      }
      ?>
      <li<?php if( $upgradeable ) echo ' class="upgradeable"' ?>>

        <?php if( !empty($packageInfo['navigation']) ): ?>
          <div class='admin_packages_options'>
            <ul>
              <?php foreach( $packageInfo['navigation'] as $navInfo ): ?>
                <li>
                  <a href="<?php echo $navInfo['href'] ?>">
                    <?php echo $navInfo['label'] ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="admin_packages_title">
          <h3>
            <?php echo $package->getMeta()->getTitle() ?>
            <span class="admin_packages_version">
              <?php echo $package->getVersion() ?>
            </span>
          </h3>
          <span class="admin_packages_author">
            by <?php echo join(', ', $package->getMeta()->getAuthors()) ?>
          </span>
          <?php if( isset($packageInfo['database']['version']) && version_compare($packageInfo['database']['version'], $package->getVersion(), '<') ): ?>
          <span class="admin_packages_warning">
            Warning: Your database structure for this package is out of date.
            The version you currently have is <?php echo $packageInfo['database']['version'] ?>.
            Please complete the installation of this package to resolve this problem.
          </span>
          <?php endif; ?>
        </div>

      </li>
    <?php endforeach; ?>
  </ul>
<?php else: ?>
  <div class="tip">
    <span>
      You do not have any packages installed yet.
    </span>
  </div>
<?php endif; ?>

<br />
<br />


<!--
<h3>
  Reference Layout:
</h3>

<ul class="admin_packages">
  <li class="admin_packages_installable">
    <div class="admin_packages_title">
      <h3>Awesome Mod</h3>
      <span class="admin_packages_info">
        <span class="admin_packages_version">3.1</span>
        <span class="admin_packages_author">by Webligo Developments</span>
      </span>
    </div>
    <div class="admin_packages_options">
      <a href="#">install</a>
    </div>
  </li>
  <li>
    <div class="admin_packages_title">
      <h3>Awesome Mod</h3>
      <span class="admin_packages_info">
        <span class="admin_packages_version">3.1</span>
        <span class="admin_packages_author">by Webligo Developments</span>
      </span>
    </div>
    <div class="admin_packages_options">
      <a href="#">disable</a>
      <span class="sep">|</span>
      <a href="#">delete</a>
    </div>
  </li>
  <li>
    <div class="admin_packages_title">
      <h3>Awesome Mod</h3>
      <span class="admin_packages_info">
        <span class="admin_packages_version">3.1</span>
        <span class="admin_packages_author">by Webligo Developments</span>
      </span>
    </div>
    <div class="admin_packages_options">
      <a href="#">disable</a>
      <span class="sep">|</span>
      <a href="#">delete</a>
    </div>
  </li>
</ul>
-->






