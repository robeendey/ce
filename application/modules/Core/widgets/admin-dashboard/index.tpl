<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div class="admin_home_dashboard">
  <h3 class="sep">
    <span>
      <?php if( $this->viewer() && $this->viewer()->getIdentity() ): ?>
        <?php echo $this->translate("%s's Dashboard", $this->viewer()->getTitle()) ?>
      <?php else: ?>
        <?php echo $this->translate("Admin Dashboard") ?>
      <?php endif; ?>
    </span>
  </h3>
  <?php if( !empty($this->notifications) ): ?>
    <ul class="admin_home_dashboard_messages">
      <?php foreach( $this->notifications as $notification ): ?>
        <li>
          <?php echo $notification ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  <ul class="admin_home_dashboard_links">
    <li>
      <ul>
        <li>
          <a href="<?php echo $this->url(array('module' => 'user', 'controller' => 'manage', 'action' => 'index'), 'admin_default', true) ?>" class="links_members">
            <?php echo $this->translate("View Members") ?>
          </a>
          (<?php echo $this->userCount ?>)
        </li>
        <li>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'index'), 'admin_default', true) ?>" class="links_abuse">
            <?php echo $this->translate("View Abuse Reports") ?>
          </a>
          <?php if( $this->reportCount > 0 ): ?>
            (<?php echo $this->reportCount ?>)
          <?php endif; ?>
        </li>
        <li>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'plugins', 'action' => 'index'), 'admin_default', true) ?>" class="links_plugins">
            <?php echo $this->translate("Manage Plugins") ?>
          </a>
          (<?php echo $this->pluginCount ?>)
        </li>
      </ul>
    </li>
    <li>
      <ul>
        <li>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'content', 'action' => 'index'), 'admin_default', true) ?>" class="links_layout">
            <?php echo $this->translate("Edit Site Layout") ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true) ?>" class="links_theme">
            <?php echo $this->translate("Edit Site Theme") ?>
          </a>
        </li>
        <li>
          <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'stats', 'action' => 'index'), 'admin_default', true) ?>" class="links_stats">
            <?php echo $this->translate("View Statistics") ?>
          </a>
        </li>
      </ul>
    </li>
    <li>
      <ul>
        <li>
          <a href="<?php echo $this->url(array('module' => 'announcement', 'controller' => 'manage', 'action' => 'create'), 'admin_default', true) ?>" class="links_announcements">
            <?php echo $this->translate("Post Announcement") ?>
          </a>
        </li>
        <li>
          <a href="http://www.socialengine.net/community" class="links_getplugins">
            <?php echo $this->translate("Get More Plugins") ?>
          </a>
        </li>
        <li>
          <a href="http://www.socialengine.net/community/mods?section=templates" class="links_getthemes">
            <?php echo $this->translate("Get More Themes") ?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>