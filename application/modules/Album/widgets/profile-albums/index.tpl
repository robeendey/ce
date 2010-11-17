<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>

<ul class="thumbs">
  <?php foreach( $this->paginator as $album ): ?>
    <li>
      <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
        <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
      </a>
      <p class="thumbs_info">
        <span class="thumbs_title">
          <?php echo $this->htmlLink($album, $this->string()->chunk(substr($album->getTitle(), 0, 45), 10)) ?>
        </span>
        <?php echo $this->translate('By');?>
        <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
        <br />
        <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
      </p>
    </li>
  <?php endforeach;?>


</ul>

<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'album_general'), $this->translate('View All Albums'), array('class' => 'buttonlink item_icon_album_photo')) ?>
<?php endif;?>