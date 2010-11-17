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

<ul class="music_browse">
  <?php foreach ($this->paginator as $playlist): ?>
  <li>
    <div class='music_browse_info'>
      <div class="music_browse_info_title">
        <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
      </div>
      <div class='music_browse_info_date'>
        Posted <?php echo $this->timestamp($playlist->creation_date) ?>
      </div>
      <div class='music_browse_info_desc'>
        <?php echo $playlist->description ?>
      </div>
    </div>
    <?php echo $this->partial('application/modules/Music/views/scripts/_Player.tpl', array('playlist'=>$playlist,'short_player'=>$this->short_player)) ?>
  </li>
  <?php endforeach; ?>
</ul>
<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'music_browse'), $this->translate('View All Playlists'), array('class' => 'buttonlink item_icon_music')) ?>
<?php endif;?>