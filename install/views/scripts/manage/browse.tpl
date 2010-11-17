<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: browse.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div>
  <?php echo $this->htmlLink(array('action' => 'index', 'reset' => false), 'Back to Manager') ?>
</div>

<br />

<?php if( !empty($this->dlFiles) ): ?>
  <div>
    <?php foreach( $this->dlFiles as $dlfile ): ?>
      <span>
        <?php echo basename($dlfile) ?>
      </span>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


<?php if( !empty($this->remotePackages) ): ?>
  <form action="<?php echo $this->url() ?>" method="post">
    <table>
      <tbody>
        <tr>
          <th>
            Download?
          </th>
          <th>
            Type
          </th>
          <th>
            Name
          </th>
          <th>
            Version
          </th>
        </tr>
        <?php foreach( $this->remotePackages as $package ): ?>
        <tr>
          <td>
            <?php echo $this->formCheckbox('dl[]', $package['type'] . ':' . $package['name'] . ':' . $package['version']) ?>
          </td>
          <td>
            <?php echo $package['type'] ?>
          </td>
          <td>
            <?php echo $package['name'] ?>
          </td>
          <td>
            <?php echo $package['version'] ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php echo $this->formButton(null, 'Submit', array('type' => 'submit')) ?>
  </form>
<?php else: ?>
  <div class="empty">
    None
  </div>
<?php endif; ?>



<ul>
  <?php foreach( (array) $this->packages as $package ): ?>
    <li>
      <?php var_dump($package) ?>
    </li>
  <?php endforeach; ?>
</ul>