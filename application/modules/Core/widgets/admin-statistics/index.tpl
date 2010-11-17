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

<h3 class="sep">
  <span>
    <?php echo $this->translate('Quick Stats') ?>
  </span>
</h3>

<table class='admin_home_stats'>
  <thead>
    <tr>
      <th colspan='3'><?php echo $this->translate('Network Information') ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><?php echo $this->translate('License Key') ?></td>
      <td colspan='2'><?php echo $this->license['key'] ?></td>
    </tr>
    <tr>
      <td><?php echo $this->translate('Created') ?></td>
      <td colspan='2'><?php echo $this->timestamp($this->site['creation']) ?></td>
    </tr>
  </tbody>
</table>
<table class='admin_home_stats'>
  <thead>
    <tr>
      <th><?php echo $this->translate('Statistics') ?></th>
      <th><?php echo $this->translate('Today') ?></th>
      <th><?php echo $this->translate('Total') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach( $this->statistics as $statistic ): ?>
      <tr>
        <td>
          <?php echo $this->translate($statistic['label']) ?>
        </td>
        <td>
          <?php echo $this->locale()->toNumber($statistic['today']) ?>
        </td>
        <td>
          <?php echo $this->locale()->toNumber($statistic['total']) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>