<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _Player.tpl 7441 2010-09-22 03:30:55Z john $
 * @author     Steve
 */
?>
<?php
  $playlist = $this->playlist;
  $songs    = (isset($this->songs) && !empty($this->songs))
            ? $this->songs
            : $playlist->getSongs();
  
  $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Music/externals/soundmanager/script/soundmanager2.js')
       ->appendFile($this->baseUrl() . '/application/modules/Music/externals/scripts/core.js')
       ->appendFile($this->baseUrl() . '/application/modules/Music/externals/scripts/player.js');

  $this->headTranslate(array(
    'Disable Profile Playlist',
    'Play on my Profile',
  ));

  // this forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page
  $random   = '';
  for ($i=0; $i<6; $i++) { $d=rand(1,30)%2; $random .= ($d?chr(rand(65,90)):chr(rand(48,57))); }
?>
<?php if (!$playlist->isViewable() && $this->message_view): ?>
  <div class="tip">
    <?php echo $this->translate('This playlist is private.') ?>
  </div>
<?php return; elseif (empty($songs) || empty($songs[0])): ?>
    <br />
    <div class="tip">
      <span>
        <?php echo $this->translate('There are no songs uploaded yet.') ?>
        <?php if ($playlist->isEditable()): ?>
          <?php echo $this->htmlLink(array('route'=>'music_edit','playlist_id'=>$playlist->getIdentity()), $this->translate('Why don\'t you add some?')) ?>
        <?php endif; ?>
      </span>
    </div>
    <br />
<?php return; endif; ?>
<div class="music_player_wrapper" id="music_player_<?php echo $random ?>">

    <div class="music_player" <?php if (isset($this->id)) echo "id='{$this->id}'" ?> <?php if ($this->short_player): ?>style="display:none;"<?php endif; ?>>
      <div class="music_player_top">
        <div class="music_player_art">
          <?php echo $this->itemPhoto($playlist, null, $playlist->getTitle()) ?>
        </div>
        <div class="music_player_info">
          <div class="music_player_controls_wrapper">
            <div class="music_player_controls_right">
              <span class="music_player_button_launch_wrapper">
                <div class="music_player_button_launch_tooltip"><?php echo $this->translate('Pop-out Player') ?></div>
                  <?php echo $this->htmlLink($this->url(array('playlist_id'=>$playlist->playlist_id,'popout'=>true), 'music_playlist'),
                              '',
                              array('class'=>'music_player_button_launch')) ?>
              </span>
            </div>
            <div class="music_player_controls_left">
              <span class="music_player_button_prev"></span>
              <span class="music_player_button_play"></span>
              <span class="music_player_button_next"></span>

              <div class="music_player_controls_volume">
                <span class="music_player_controls_volume_toggle"></span>
                <span class="music_player_controls_volume_bar"><span class="volume_bar_1"></span></span>
                <span class="music_player_controls_volume_bar"><span class="volume_bar_2"></span></span>
                <span class="music_player_controls_volume_bar"><span class="volume_bar_3"></span></span>
                <span class="music_player_controls_volume_bar"><span class="volume_bar_4"></span></span>
                <span class="music_player_controls_volume_bar"><span class="volume_bar_5"></span></span>
              </div>
            </div>
          </div>
          <div class="music_player_trackname"></div>
          <div class="music_player_scrub">
            <div class="music_player_scrub_cursor"></div>
            <div class="music_player_scrub_downloaded"></div>
          </div>
          <div class="music_player_time">
            <div class="music_player_time_elapsed"></div>
            <div class="music_player_time_total"></div>
          </div>
        </div>
      </div>
      <ul class="music_player_tracks playlist_<?php echo $playlist->getIdentity() ?>">

        <?php foreach ($songs as $song): if (!empty($song)): ?>
        <li>
          <div class="music_player_tracks_add_wrapper">
            <div class="music_player_tracks_add_tooltip"><?php echo $this->translate('Add to my Playlist') ?></div>
            <?php if ($this->viewer()->getIdentity()) echo $this->htmlLink(
                    $this->url(array('playlist_id'=>$song->playlist_id, 'song_id'=>$song->song_id), 'music_playlist_append'),
                    '',
                    array('class'=>'smoothbox music_player_tracks_add') ) ?>
          </div>
          <div class="music_player_tracks_name" title="<?php echo $song->getTitle() ?>">
            <?php echo $this->htmlLink(
                         $song->getFilePath(),
                         strlen($song->getTitle())>53?substr($song->getTitle(),0,50).'...':$song->getTitle(),
                         array(
                            'class'=>'music_player_tracks_url',
                            'type'=>'audio',
                            'rel'=>$song->song_id) ) ?>
            <span class="music_player_tracks_plays">
              (<span><?php echo $song->playCountLanguagified() ?></span>)
            </span>

          </div>
        </li>
        <?php endif; endforeach; ?>

      </ul>
    </div>

    <?php if ($this->short_player): ?>
      <div class="music_player playlist_short_player">
        <div class="music_player_top">
          <div class="music_player_info">
            <div class="music_player_controls_wrapper">
              <div class="music_player_controls_left">
                <span class="music_player_button_play"></span>
                <div class="playlist_short_player_title">
                  <?php if (!empty($songs) && !empty($songs[0])) echo $songs[0]->getTitle() ?>
                </div>
                <div class="playlist_short_player_tracks">
                  <?php $songCount = count($songs); ?>
                  <?php echo $this->translate(array("%s track", "%s tracks", $songCount), $this->locale()->toNumber($songCount)) ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

</div>
<script type="text/javascript">
//<![CDATA[
en4.core.runonce.add(function(){
  soundManager.onready(function(){
    
      var par = $('music_player_<?php echo $random ?>');
      var el  = par.getElement('div.music_player');

      // show the entire player
      if (!par.getElement('div.playlist_short_player')) {
        if (!el.hasClass('playlist_player_loaded')) {
          playlists.push( new playlist(el) );
          el.addClass('playlist_player_loaded');
        }

      // show the short player first
      } else {
        par.getElement('div.music_player:not(div.playlist_short_player)').hide();
        //par.getElement('div.playlist_short_player div.music_player_button_play').removeEvents('click').addEvent('click', function(){
        par.getElement('div.playlist_short_player').addEvent('click', function(){
            var par = $('music_player_<?php echo $random ?>');
            var el  = par.getElement('div.music_player');
            el.show();
            par.getElement('div.playlist_short_player').hide();

            if (!el.hasClass('playlist_player_loaded')) {
                var player = new playlist(el);
                playlists.push(player);
                player.play();
                el.addClass('playlist_player_loaded');
            }
        });
      }


  });
});
//]]>
</script>
