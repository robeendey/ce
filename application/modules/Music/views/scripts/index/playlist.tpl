<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: playlist.tpl 7441 2010-09-22 03:30:55Z john $
 * @author     Steve
 */

// this is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs    = $playlist->getSongs();
?>

<?php if ($this->popout): ?>
<?php $this->headTitle($playlist->getTitle(), Zend_View_Helper_Placeholder_Container_Abstract::SET) ?>
  <div class="music_playlist_popout_wrapper">
    <div class="music_playlist_info_title">
      <h3><?php echo $playlist->getTitle() ?></h3>
    </div>
    <div class="music_playlist_info_date">
          <?php echo $this->translate('Created %1$s by %2$s', $this->timestamp($playlist->creation_date), $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle())) ?>
    </div>
    <?php echo $this->partial('_Player.tpl', array('playlist'=>$this->playlist,'popout'=>true)) ?>
  </div>
<?php return; endif; ?>

<h2>
  <?php echo $this->translate('Music by %s', $this->htmlLink($this->playlist->getOwner(), $this->playlist->getOwner()->getTitle()) ) ?>
</h2>

<div class="music_playlist">
  <div class="music_browse_author_photo">
    <?php echo $this->htmlLink($playlist->getOwner(), $this->itemPhoto($playlist->getOwner(), 'thumb.icon') ) ?>
  </div>

  <div class="music_playlist_options">
    <?php if ($playlist->isEditable())
      echo $this->htmlLink($playlist->getEditHref(),
        $this->translate('Edit Playlist'),
        array('class'=>'buttonlink icon_music_edit'
      )) ?>
    <?php if ($playlist->isDeletable())
      echo $this->htmlLink($playlist->getDeleteHref(),
        $this->translate('Delete Playlist'),
        array('class'=>'buttonlink smoothbox icon_music_delete'
      )) ?>
    <?php if ($playlist->getOwner() == Engine_Api::_()->user()->getViewer())
      echo $this->htmlLink($this->url(array(
        'playlist_id'=>$playlist->playlist_id,'controller'=>'index','module'=>'music', 'action'=>'set-profile-playlist'), 'default'),
        $this->translate($playlist->profile ? 'Disable Profile Playlist' : 'Play on my Profile'),
        array(
          'class' => 'buttonlink music_set_profile_playlist ' . ( $playlist->profile ? 'icon_music_disableonprofile' : 'icon_music_playonprofile' )
        )
      ) ?>
  </div>

  <div class="music_playlist_info">
    <div class="music_playlist_info_title">
      <h3><?php echo $playlist->getTitle() ?></h3>
      <p><?php echo $playlist->description ?></p>
    </div>
    <div class="music_playlist_info_date">
          <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
          <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
    </div>

    <?php echo $this->partial('_Player.tpl', array('playlist'=>$playlist)) ?>
    
    <?php echo $this->action("list", "comment", "core", array("type"=>"music_playlist", "id"=>$playlist->playlist_id)) ?>
  </div>
</div>

