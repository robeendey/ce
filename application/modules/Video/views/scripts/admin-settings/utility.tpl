<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: utility.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>

<h2><?php echo $this->translate("Videos Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
	<?php echo $this->translate("This page contains utilities to help configure and troubleshoot the video plugin.") ?>
</p>
<br/>

<div class="settings">
  <form>
  <h2><?php echo $this->translate("Ffmpeg Version") ?></h2>
  <?php echo $this->translate("This will display the current installed version of ffmpeg.") ?><br/>
  <textarea><?php echo $this->version;?></textarea><br/><br/>
  <h2><?php echo $this->translate("Supported Video Formats") ?></h2>
  <?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this page for more info.') ?><br/>
  <textarea><?php echo $this->format;?></textarea><br/><br/>
  <br />
  <?php if( TRUE ): ?>


  <?php else:?>
  <?php endif; ?>
  </form>
</div>