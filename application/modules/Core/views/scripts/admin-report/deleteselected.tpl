<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: deleteselected.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>

<div class="settings">
<div class='global_form'>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected reports?") ?></h3>
      <p>
        <?php echo $this->translate(
                array("Are you sure that you want to delete this %d report? It will not be recoverable after being deleted.",
                      "Are you sure that you want to delete the %d reports? They will not be recoverable after being deleted.",
                      $this->count), $this->count) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids; ?>"/>

        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate("or") ?> <a href='<?php echo $this->url(array('module'=>'admin','controller'=>'report'), 'default', true) ?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
</div>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
