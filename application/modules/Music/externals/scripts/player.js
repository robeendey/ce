if (!$type(playlists))
var playlists = [];

soundManager.url  =  $$('head base')[0].href;// + en4.core.baseUrl + (en4.core.baseUrl.match(/\/$/)?'':'/');
soundManager.url += 'application/modules/Music/externals/soundmanager/swf/';
soundManager.debugMode   = false;
soundManager.consoleOnly = true;
soundManager.allowPolling = true;
soundManager.flashVersion = 9;
soundManager.flash9Options =
soundManager.useMovieStar = true;

if (!$type(playlist_options))
var playlist_options = {
  mute: false,
  volume: (Cookie.read('music_volume')?Cookie.read('music_volume'):85)
}

// preload pause button element as defined in CSS class '.music_player_button_pause'
window.addEvent('load', function(){
  new Element('div', {
    'id': 'pause_preloader',
    'class': 'music_player_button_pause',
    'style': 'position: absolute; top: -9999px; left: -9999px;'
  }).inject(document.body).destroy();
});

if (!$type(playlist))
var playlist = new Class({
  mode:      'linear',
  repeat:    false,
  container: null,
  songs:     [],
  tallied:   {},
  slider:    null,
  sound:     null,
  selected:  0,
  slider_w:  385, // pixels wide
  container_w: 115, // pixels of the "chrome" surrounding the slider

  self: this,

  play : function(song_id) {
    var sID   = $type(song_id)
              ? song_id
              : this.selected;
    var newsound = this.create(sID);
    if (this.sound && newsound.sID == this.sound.sID && this.sound.playState)
        this.sound.togglePause();
    else {
        this.selected = sID;
        this.sound    = newsound;
        soundManager.stopAll();
        this.sound.setPosition(0);
        this.sound.play();
        $each(playlists, function(pl){
            pl.slider.set(0);
            if (pl.sound)
              pl.container.getElement('div.music_player_time_elapsed').set('text', '0:00');
        });
    }
    
    this.setTitle(this.songs[sID].getParent().get('title'));
  },

  create: function(song_id) {
    if (!$type(this.songs[song_id]))
        return;
    var sound_id   = '_song_' + this.container.getParent('.music_player_wrapper').id +'_'+ this.songs[song_id].href;
    if (soundManager.getSoundById(sound_id))
        return soundManager.getSoundById(sound_id);
    var self       = this;
    var sound_opts = {
          id:   sound_id,
          url:  this.songs[song_id].href,
          autoload:     true,
          useVideo:     false,
          volume:       playlist_options.mute ? 0 : playlist_options.volume,
          onload:       function(){self.setDuration(self);},
          whileloading: function(){self.setDuration(self);},
          whileplaying: function(){self.setElapsed(self);self.setScrub(self);},
          onplay:       function(){self.container.getElement('.music_player_button_play').addClass('music_player_button_pause');self.logPlay(self);},
          onresume:     function(){self.container.getElement('.music_player_button_play').addClass('music_player_button_pause');},
          onpause:      function(){self.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');},
          onstop:       function(){self.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');},
          onfinish:     function(){self.container.getElement('.music_player_button_play').removeClass('music_player_button_pause');self.playNext();},
          onbeforefinish:     function(){var nextsong = self.create(song_id+1); nextsong.load();}
    };
    if (sound_id.match(/\.mp3$/))
        soundManager.createSound(sound_opts);
    else
        soundManager.createVideo(sound_opts);
    return soundManager.getSoundById(sound_id);
  },

  logPlay: function(){
    var song_id = this.songs[this.selected].rel;
    var playlist_id = this.container.getElement('ul.music_player_tracks').className.split('_');
        playlist_id = playlist_id[playlist_id.length-1];

    var self = this;
    this.songs.each(function(song, index){
      if (index == self.selected)
        song.getParent('li').addClass('song_playing');
      else
        song.getParent('li').removeClass('song_playing');
    });

    if (!this.tallied[song_id]) {
      this.tallied[song_id] = true;
      new Request.JSON({
        url: $$('head base[href]')[0].get('href') + 'music/index/song-play-tally',
        noCache: true,
        data: {
          format: 'json',
          song_id: song_id,
          playlist_id: playlist_id
        },
        onSuccess: function(responseJSON) {
          if (responseJSON && $type(responseJSON) == 'object' && responseJSON.song && responseJSON.song.play_count)
            self.songs[self.selected].getParent('li').getElement('.music_player_tracks_plays span').set('text', responseJSON.play_count);
        }
      }).send();
    }
  },
  // called by Slider after moving position
  seekTo: function(pos){
    if (this.sound) {
      var ms_total = this.sound.durationEstimate;
      var ms_dest  = Math.round(ms_total * (pos/this.slider_w));
      var diff     = Math.abs(ms_dest - this.sound.position);
      if (this.slider.element.hasClass('mousedown') || diff > 2000)
        this.sound.setPosition(ms_dest);
    }
  },
  // called by soundManager.whileplaying
  setScrub:    function(pl) {
    var self = pl;
    if (self.sound && !this.slider.element.hasClass('mousedown')) {
      var percent = (self.sound.position/self.sound.durationEstimate)*100;
      var steps = Math.round(percent * (self.slider_w/100));
      self.slider.set(steps);
    }
  },
  playNext: function(){
    if (this.songs[this.selected+1])
      this.play( this.selected+1 );
    else if (this.repeat)
      this.play(0);
    else
      this.slider.set(0);
  },
  playPrev: function(){
    if (this.songs.length == 1)
      this.seekTo(0);
    else if (this.selected == 0 && this.repeat)
      this.play( this.songs.length-1 );
    else if (this.selected == 0)
      this.seekTo(0);
    else if (this.selected > 0)
      this.play(this.selected-1);
  },
  launch:   function(){
    var href  = this.container.getElement('a.music_player_button_launch').href;
    window.open(href, 'player', 'status=0,toolbar=0,location=0,menubar=0,directories=0,scrollbars=0,resizable=0,height=500,width=600');
  },

  getDuration: function() {this.container.getElement('div.music_player_time_total').get('text');},
  getElapsed:  function() {this.container.getElement('div.music_player_time_elapsed').get('text');},
  getTitle:    function() {this.container.getElement('div.music_player_trackname').get('text');},
  setTitle:    function(sText) {this.container.getElement('div.music_player_trackname').set('text', sText);},
  setDownloaded: function(pl) {
    var self = pl;
    if (self.sound) {
      var percent = 100;
      if (self.sound.isBuffering)
        percent = (self.sound.position/self.sound.durationEstimate)*100;
      self.getElement('div.music_player_scrub_downloaded').setStyle('width', percent);
    }
  },
  setElapsed:  function(pl) {
    var self = pl;
    if (self.sound) {
      var ms  = self.sound.position;
      var d   = new Date(ms);
      var hms = d.getMinutes().toString() +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
      self.container.getElement('div.music_player_time_elapsed').set('text', hms);
    }
  },
  setDuration: function(pl) {
    var self = pl;
    if (self.sound) {
      var ms  = self.sound.durationEstimate;
      var d   = new Date(ms);
      var hms = d.getMinutes().toString() +':'+ (d.getSeconds().toString().length==1?'0':'') + d.getSeconds().toString();
      self.container.getElement('div.music_player_time_total').set('text', hms);
    }
  },
  setVolume: function(volume) {
    playlist_options.mute   = false;
    playlist_options.volume = volume;
    Cookie.write('music_volume', volume, {
      duration: 7 // days
    });
    if (this.sound) {
      this.sound.unmute();
      this.sound.setVolume(volume);
    }

    this.container.getElement('.music_player_controls_volume_toggle').removeClass('music_player_controls_volume_toggle_mute');
    if ($type(playlists)) {
      playlists.each(function(pl){
        pl.container.getElement('.music_player_controls_volume_toggle').removeClass('music_player_controls_volume_toggle_mute');
        if (pl.sound)
            pl.sound.setVolume(volume);
        pl.container.getElements('.music_player_controls_volume_bar span').each(function(el){
          var level = el.className.split('_');
              level = level[level.length-1];
          if ((level*20) <= playlist_options.volume)
              el.getParent().addClass('music_player_controls_volume_enabled');
          else
              el.getParent().removeClass('music_player_controls_volume_enabled');
        });
      });
    }
  },
  toggleMute: function(){
    playlist_options.mute = !playlist_options.mute;
    if (false == playlist_options.mute && this.sound) {
      this.sound.unmute();
      this.sound.setVolume(playlist_options.volume);
    } else if (this.sound)
      this.sound.mute();
    // do the same for all the other playlists
    if ($type(playlists))
      playlists.each(function(pl){
        var mute_btn = pl.container.getElement('.music_player_controls_volume_toggle');
        var vol_btns = pl.container.getElements('.music_player_controls_volume_bar');
        if (playlist_options.mute) {
          mute_btn.addClass('music_player_controls_volume_toggle_mute');
          vol_btns.hide();
          if (pl.sound)
              pl.sound.mute();
        } else {
          mute_btn.removeClass('music_player_controls_volume_toggle_mute');
          vol_btns.show();
          if (pl.sound) {
              pl.sound.unmute();
              pl.sound.setVolume(playlist_options.volume);
          }
        }
      });
  },

  initialize: function(el) {
    // attach self for event handlers
    var self = this;
    // attach container to this player
    this.container = $(el);
    this.container.getElement('div.music_player_scrub_downloaded').hide();
    this.songs     = this.container.getElements('a.music_player_tracks_url');
    // attach button events to this player
    this.container.getElement('.music_player_button_play').addEvent('click', function(){
      self.play();
      return false;
    });
    this.container.getElement('.music_player_button_prev').addEvent('click', function(){
      self.playPrev();
      return false;
    });
    this.container.getElement('.music_player_button_next').addEvent('click', function(){
      self.playNext();
      return false;
    });
    this.container.getElement('.music_player_button_launch').addEvent('click', function(){
      self.launch();
      return false;
    });
    // volume controls
    this.container.getElement('.music_player_controls_volume_toggle').addEvent('click', function(){
      self.toggleMute();
    });
    this.container.getElements('.music_player_controls_volume_bar').addEvent('click', function(e){
      var bar   = e.target;
      if (bar.hasClass('music_player_controls_volume_bar'))
          bar   = bar.getElement('span');
      var level = bar.className.split('_');
          level = parseInt( level[level.length-1] );
      self.setVolume(level*20);
    }).addEvent('mouseover', function(e){
      var bar   = e.target;
      if (bar.hasClass('music_player_controls_volume_bar'))
          bar   = bar.getElement('span');
      var level = bar.className.split('_');
          level = parseInt( level[level.length-1] );
    }).addEvent('mouseover', function(e){
      var bar   = e.target;
      if (bar.hasClass('music_player_controls_volume_bar'))
          bar   = bar.getElement('span');
      var level = bar.className.split('_');
          level = parseInt( level[level.length-1] );

    });
    this.setVolume(playlist_options.volume);

    // attach scrub bar
    var scrubBar  = this.container.getElement('div.music_player_scrub');
    var chrome = this.container.getElement('.music_player_top').measure(function(){ return this.getDimensions(); });
    var img    = this.container.getElement('.music_player_art').measure(function(){ return this.getDimensions(); });
    if (this.container.getElement('.music_player_art').isDisplayed()) {
      this.slider_w = chrome.width - img.width;
    } else
      this.slider_w = chrome.width;
    scrubBar.setStyle('width', this.slider_w+'px');
    scrubBar.addEvent('mousedown', function(){
      this.addClass('mousedown');
    });
    scrubBar.addEvent('mouseup', function(){
      this.removeClass('mousedown');
    });
    this.slider = new Slider(
      this.container.getElement('div.music_player_scrub'),
      this.container.getElement('div.music_player_scrub_cursor'), {
        snap: false,
        offset: 0,
        range: [0,this.slider_w],
        wheel: true,
        steps: this.slider_w,
        initialStep: 0,
        onComplete: function(step) {
          self.seekTo(step);
        }
      }
    );
    //if( $type(en4.orientation) && en4.orientation == 'rtl' ) {
    //  this.slider.property = 'right';
    //}
    // attach song list to player
    this.container.getElements('ul.music_player_tracks li').addEvent('click', function(e){
      if (!this.hasClass('smoothbox')) {
        self.play( this.getAllPrevious().length );
        if ($type(e))
          e.stop();
        else
          return false;
      }
    });
    this.container.getElements('a.music_player_tracks_url').addEvent('click', function(e){
      if (soundManager.supported()) {
        this.getParent('li').fireEvent('click');
        if ($type(e))
          e.stop();
        else
          return false;
      }
    });
    if (this.songs.length) {
      //this.create(0).load();
      if (this.songs[0])
        this.setTitle(this.songs[0].getParent().get('title'));
    }
  } // end initialize

});
