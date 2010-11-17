<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manage.tpl 7441 2010-09-22 03:30:55Z john $
 * @author     Steve
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Music');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>

<div class='layout_right'>
  <?php echo $this->search_form->render($this) ?>
  <script type="text/javascript">
  //<![CDATA[
    $('sort').addEvent('change', function(){
      $(this).getParent('form').submit();
    });
  //]]>
  </script>
</div>

<div class='layout_middle'>
  <?php if (0 == count($this->paginator) ): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('There is no music uploaded yet.') ?>
        <?php if (TRUE): // @todo check if user is allowed to create a music ?>
        <?php echo $this->htmlLink(array('route'=>'music_create'), $this->translate('Why don\'t you add some?')) ?>
        <?php endif; ?>
      </span>
    </div><!-- one more ending div for 'layout_middle' --></div>
  <?php return; endif; ?>
  <ul class="music_browse">
    <?php foreach ($this->paginator as $playlist): ?>
      <li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
        <div class="music_browse_options">
          <?php if ($playlist->isDeletable() || $playlist->isEditable()): ?>
            <ul>
              <?php if ($playlist->isEditable()): ?>
                <li>
                  <?php echo $this->htmlLink($playlist->getEditHref(),
                    $this->translate('Edit Playlist'),
                    array('class'=>'buttonlink icon_music_edit'
                    )) ?>
                </li>
              <?php endif; ?>
              <?php if ($playlist->isDeletable()): ?>
                <li>
                  <?php echo $this->htmlLink($playlist->getDeleteHref(),
                    $this->translate('Delete Playlist'),
                    array('class'=>'buttonlink smoothbox icon_music_delete'
                  )) ?>
                </li>
              <?php endif; ?>
              <?php if ($playlist->getOwner() == Engine_Api::_()->user()->getViewer()): ?>
                <li>
                  <?php echo $this->htmlLink($this->url(array('playlist_id'=>$playlist->playlist_id,'action'=>'set-profile-playlist','controller'=>'index','module'=>'music'), 'default'),
                    $this->translate($playlist->profile ? 'Disable Profile Playlist' : 'Play on my Profile'),
                    array(
                      'class' => 'buttonlink music_set_profile_playlist ' . ( $playlist->profile ? 'icon_music_disableonprofile' : 'icon_music_playonprofile' )
                    )
                  ) ?>
                </li>
              <?php endif; ?>
            </ul>
          <?php endif; ?>
        </div>
        <div class="music_browse_info">
          <div class="music_browse_info_title">
            <h3><?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?></h3>
          </div>
          <div class="music_browse_info_date">
            <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
            <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
            -
            <?php echo $this->htmlLink($playlist->getHref(),  $this->translate(array('%s comment', '%s comments', $playlist->getCommentCount()), $this->locale()->toNumber($playlist->getCommentCount()))) ?>
          </div>
          <div class="music_browse_info_desc">
            <?php echo $playlist->description ?>
          </div>
          <?php echo $this->partial('_Player.tpl', array('playlist'=>$playlist)) ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php echo $this->paginationControl($this->paginator); ?>

</div>
