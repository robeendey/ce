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

<ul>
  <li>
    <a class="buttonlink" href="<?php echo $this->url(array('action' => 'sanity')) ?>">
      Requirement and Dependency Check
    </a>
    <p class="buttontext">
      Double check the requirements and dependencies of your installed packages.
    </p>
    <br />
  </li>
  
  <li>
    <a class="buttonlink" href="<?php echo $this->url(array('action' => 'compare')) ?>">
      Search for Modified Files
    </a>
    <p class="buttontext">
      Lists files that have been modified since installation. You can view a
      side-by-side diff of the files if you upload the original package.
    </p>
    <br />
  </li>

  <li>
    <a class="buttonlink" href="<?php echo $this->url(array('action' => 'php')) ?>">
      PHP Info
    </a>
    <p class="buttontext">
      Displays the results of the phpinfo() function.
    </p>
    <br />
  </li>

  <?php if( $this->hasAdminer ): ?>
  <li>
    <a class="buttonlink" href="<?php echo $this->url(array('action' => 'adminer')) ?>/">
      Adminer
    </a>
    <p class="buttontext">
      Adminer is a MySQL database management utility, similar to phpMyAdmin.
    </p>
    <br />
  </li>
  <?php endif; ?>
</ul>
