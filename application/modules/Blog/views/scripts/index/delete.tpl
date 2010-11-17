<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Blogs');?>
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

<div class='global_form'>
  <form method="post" class="global_form" action="<?php echo $this->url() ?>">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Delete Blog Entry?');?>
        </h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the blog entry with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->blog->title,$this->timestamp($this->blog->modified_date)); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete');?></button>
          <?php echo $this->translate('or');?> <a href='<?php echo $this->url(array('action' => 'manage'), 'blog_general', true) ?>'><?php echo $this->translate('cancel');?></a>
        </p>
      </div>
    </div>
  </form>
</div>