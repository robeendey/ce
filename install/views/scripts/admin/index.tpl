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

<div>
  <a class="buttonlink" href="<?php echo $this->url(array('action' => 'create')) ?>">Create New Login</a>
</div>

<br />

<ul class="package_users_list">
  <li class="header">
    <span class="package_users_list_type">
      Type
    </span>
    <span class="package_users_list_name">
      Name
    </span>
    <span class="package_users_list_options">
      Options
    </span>
  </li>
  <?php foreach( $this->users as $user ): ?>
    <li>
      <span class="package_users_list_type">
        (<?php echo $user['type'] == 'digest' ? 'auth file' : $user['type'] ?>)
      </span>
      <span class="package_users_list_name">
        <?php echo $user['name'] ?>
      </span>
      <span class="package_users_list_options">
        <?php if( $user['type'] == 'digest' ): ?>
          <a href="<?php echo $this->url(array('action' => 'edit')) ?>?username=<?php echo $user['name'] ?>">edit</a> |
          <a href="<?php echo $this->url(array('action' => 'delete')) ?>?username=<?php echo $user['name'] ?>">remove</a>
        <?php elseif( $user['type'] == 'database' ): /* ?>
          <a href="<?php echo $this->url(array('action' => 'edit')) ?>">edit</a> |
          <a href="<?php echo $this->url(array('action' => 'edit')) ?>">remove</a>
        <?php */ endif; ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>