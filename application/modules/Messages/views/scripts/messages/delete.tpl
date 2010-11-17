<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>

    <script type="text/javascript">
      <?php foreach ($this->deleted_conversation_ids as $conversation_id): ?>
        parent.$('message_conversation_<?php echo $conversation_id ?>').destroy();
      <?php endforeach; ?>
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate('The selected messages have been deleted.') ?>
    </div>

  <?php else: // success == false ?>

  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3>
        <?php echo $this->translate('Delete Message(s)?') ?>
      </h3>
      <p>
        <?php echo $this->translate('Are you sure that you want to delete the selected message(s)? This action cannot be undone.') ?>
      </p>

      <p>&nbsp;</p>

      <p>
        <input type="hidden" name="message_ids" value="<?php echo $this->message_ids?>"/>
        <button type='submit'><?php echo $this->translate('Delete') ?></button>
        <?php echo $this->translate('or') ?>
        <a href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('cancel') ?></a>
      </p>
    </div>
  </form>
  <?php endif; // success ?>

</div>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>

