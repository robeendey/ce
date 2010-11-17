<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
?>

<div class='global_form_popup'>
  <?php if ($this->success): ?>

    <script type="text/javascript">
      parent.$('poll-item-<?php echo $this->poll_id ?>').destroy();
      setTimeout(function() {
        parent.Smoothbox.close();
      }, 1000 );
    </script>
    <div class="global_form_popup_message">
      <?php echo $this->translate("Your poll has been deleted.") ?>
    </div>

  <?php else: // success == false ?>

  <form method="POST" action="<?php echo $this->url() ?>">
    <div>
      <h3><?php echo $this->translate("Delete Poll?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this Poll? This action cannot be undone.") ?>
      </p>

      <p>&nbsp;</p>

      <p>
        <input type="hidden" name="poll_id" value="<?php echo $this->poll_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate("or") ?>
        <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">
          <?php echo $this->translate("cancel") ?>
        </a>
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

