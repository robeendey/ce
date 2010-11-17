<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Music
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: playlist-append.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>
<div class='global_form_popup'>
    <?php if (isset($this->success)): ?>
      <div class="global_form_popup_message">
      <?php if ($this->success): ?>

       <p><?php echo $this->message ?></p>
       <br />
       
       <button onclick="parent.Smoothbox.close();">
         &laquo; <?php echo $this->translate('Return to page') ?>
       </button>

       <button onclick="parent.window.location.href='<?php echo $this->url(array('playlist_id' => $this->playlist_id), 'music_playlist') ?>'">
         <?php echo $this->translate('Go to my playlist') ?> &raquo;
       </button>
       

      <?php elseif (!empty($this->error)): ?>
        <pre style="text-align:left"><?php echo $this->error ?></pre>
      <?php else: ?>
        <p><?php echo $this->translate('There was an error processing your request.  Please try again later.') ?></p>
      <?php endif; ?>
      </div>
    <?php return; endif; ?>

    <?php echo $this->form->render($this) ?>
</div>

<script type="text/javascript">
function updateTextFields() {
  if ($('playlist_id').value == 0) {
    $('title-wrapper').show();
  } else {
    $('title-wrapper').hide();
  }
  parent.Smoothbox.instance.doAutoResize();
}
en4.core.runonce.add(updateTextFields);
</script>