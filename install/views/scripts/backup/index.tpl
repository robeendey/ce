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
  <?php echo $this->htmlLink(array('route'=>'manage'), '&laquo; Back to Package Manager', array()) ?>
</h3>

<h2>Backup Manager</h2>

<p>Create, restore, and manage backups of your site.</p>

<br />

<div>
  <a class="buttonlink admin_packages_add" href="<?php echo $this->url(array('action' => 'create')) ?>">
    Make a Backup
  </a>
</div>

<br />

<?php if( empty($this->backups) ): ?>

  <div class="tip">
    There are currently no backups.
  </div>

<?php else: ?>

  <ul>
    <?php foreach( $this->backups as $backup ): ?>
      <li>
        <span>
          <?php echo $backup ?>
        </span>
        <span>
          <a href="<?php echo $this->url(array('action' => 'download')) ?>?backup=<?php echo basename($backup) ?>">download</a>
          <a href="<?php echo $this->url(array('action' => 'delete')) ?>?backup=<?php echo basename($backup) ?>">delete</a>
          <a href="<?php echo $this->url(array('action' => 'restore')) ?>?backup=<?php echo basename($backup) ?>">restore</a>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>

<?php endif; ?>