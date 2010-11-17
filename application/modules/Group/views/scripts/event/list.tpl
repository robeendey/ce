<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: list.tpl 7244 2010-09-01 01:49:53Z john $
 * @author	   Sami
 */
?>

<h2>
  <?php echo $this->group->__toString() ?>
  <?php echo $this->translate('&#187; Photos');?>
</h2>

<div>
  <?php if( $this->canUpload ): ?>
    <?php echo $this->htmlLink(array(
        'route' => 'group_extended',
        'controller' => 'photo',
        'action' => 'upload',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Upload Photos'), array(
        'class' => 'buttonlink icon_group_photo_new'
    )) ?>
  <?php endif; ?>
</div>

<?php if( $this->paginator->count() > 0 ): ?>
  <br />
  <?php echo $this->paginationControl($this->paginator); ?>
  <br />
<?php endif; ?>

<ul class='group_thumbs'>
  <?php foreach( $this->paginator as $photo ): ?>
    <li class="group_album_thumb_notext">
      <div class='group_album_thumb_wrapper'>
          <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal')) ?>
      </div>
    </li>
  <?php endforeach;?>
</ul>

<?php if( $this->paginator->count() > 0 ): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
  <br />
<?php endif; ?>