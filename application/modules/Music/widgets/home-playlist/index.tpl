<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7443 2010-09-22 07:25:41Z john $
 * @author     John
 */
?>

<div id="profile_music_player">
  <h3><?php echo $this->htmlLink($this->playlist, $this->playlist->getTitle()) ?></h3>
  <?php echo $this->partial('application/modules/Music/views/scripts/_Player.tpl', array(
    'playlist' => $this->playlist,
    'id' => 'music_profile_player'
  )) ?>
  <script type="text/javascript">
  //<![CDATA[
  var music_profile_player = $('music_profile_player');
  if( music_profile_player ) {
    music_profile_player.setStyles({
      width : '160px',
      marginTop : 0
    });
  }
  //]]>
  </script>
</div>
