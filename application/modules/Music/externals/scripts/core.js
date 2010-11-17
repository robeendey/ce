en4.core.runonce.add(function(){
    // ADD TO PLAYLIST
    $$('a.music_add_to_playlist').addEvent('click', function(){
      $('song_id').value = this.id.substring(5);
      Smoothbox.open( $('music_add_to_playlist'), { mode: 'Inline' } );
      var pl = $$('#TB_ajaxContent > div')[0];
      pl.show();
    });


    // PLAY ON MY PROFILE
    $$('a.music_set_profile_playlist').addEvent('click', function(){
      var url_part    = this.href.split('/');
      var playlist_id = 0;
      $each(url_part, function(val, i) {
        if (val == 'playlist_id')
          playlist_id = url_part[i+1];
      });
      new Request.JSON({
        method: 'post',
        url: this.href,
        noCache: true,
        data: {
          'playlist_id': playlist_id,
          'format': 'json'
        },
        onSuccess: function(json){
          var link  = $$('a.music_set_profile_playlist[href$=playlist_id/'+json.playlist_id+']')[0];
          if (json && json.success) {
            $$('a.music_set_profile_playlist')
              .set('text', en4.core.language.translate('Play on my Profile'))
              .addClass('icon_music_playonprofile')
              .removeClass('icon_music_disableonprofile')
              ;
            if( json.enabled ) {
              link
                .set('text', en4.core.language.translate('Disable Profile Playlist'))
                .addClass('icon_music_disableonprofile')
                .removeClass('icon_music_playonprofile')
                ;
            }
          }
        }
      }).send();
      return false;
    });
    
});