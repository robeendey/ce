<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>

<h2><?php echo $this->translate("Music Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("Title") ?></th>
        <th><?php echo $this->translate("Owner") ?></th>
        <th><?php echo $this->translate("Songs") ?></th>
        <th><?php echo $this->translate("Plays") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' value="<?php echo $item->getIdentity() ?>"/></td>
          <td><?php echo $item->getIdentity() ?></td>
          <td><?php echo $item->getTitle() ?></td>
          <td><?php echo $item->getOwner()->getTitle() ?></td>
          <td><?php echo count($item->getSongs()) ?>
          <td><?php echo $this->locale()->toNumber($item->play_count) ?></td>
          <td><?php echo $item->creation_date ?></td>
          <td>
            <?php echo $this->htmlLink($item->getHref(), 'play') ?>
            |
            <?php echo $this->htmlLink(array('route' => 'default', 'module'=>'music','controller'=>'index', 'action' => 'delete', 'playlist_id' => $item->getIdentity()), $this->translate('delete'), array(
              'class' => 'smoothbox',
            )) ?>
          </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <br />
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no songs posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
