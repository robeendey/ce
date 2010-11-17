<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 7320 2010-09-08 21:25:25Z shaun $
 * @author     Jung
 */
?>

<?php if( !$this->blog || ($this->blog->draft==1 && !$this->blog->isOwner($this->viewer()))): ?>
<?php echo $this->translate('The blog you are looking for does not exist or has not been published yet.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<script type="text/javascript">
  var categoryAction = function(category){
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction =function(tag){
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('filter_form').submit();
  }
</script>

<div class='layout_right'>
  <div class='blogs_gutter'>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner), array('class' => 'blogs_gutter_photo')) ?>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'blogs_gutter_name')) ?>

    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->gutterNavigation)
        ->setUlClass('navigation blogs_gutter_options')
        ->render();
    ?>
    
    <h4><?php echo $this->translate('Search Blogs');?></h4>
    
    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('user_id' => $this->blog->owner_id), 'blog_view') ?>'>
      <input id="search" name="search" type='text' />
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
    </form>

    <?php if (count($this->userCategories )):?>
      <h4><?php echo $this->translate('Categories')?></h4>
      <ul>
          <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(0);'><?php echo $this->translate('All Categories')?></a></li>
          <?php foreach ($this->userCategories as $category): ?>
            <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);'><?php echo $category->category_name?></a></li>
          <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    
    <?php if (count($this->userTags )):?>
      <h4><?php echo $this->translate('%1$s\'s Tags', $this->owner->getTitle()); ?></h4>
      <ul>
        <?php foreach ($this->userTags as $tag): ?>
          <a href='javascript:void(0);'onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' >#<?php echo $tag->text?></a>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
      
    <?php if (count($this->archive_list )):?>
      <h4><?php echo $this->translate('Archives');?></h4>
      <ul>
        <?php foreach ($this->archive_list as $archive): ?>
        <li>
          <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>><?php echo $archive['label']?></a>
        </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
      
  </div>
</div>

<div class='layout_middle'>

  <?php if ($this->blog->owner_id == $this->viewer->getIdentity()&&$this->blog->draft == 1):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('This blog entry has not been published. You can publish it by %1$sediting the entry%2$s.', '<a href="'.$this->url(array('blog_id' => $this->blog->blog_id), 'blog_edit', true).'">', '</a>'); ?>
      </span>
    </div>
    <br/>
  <?php endif; ?>

  <h2>
    <?php echo $this->translate('%1$s\'s Blog', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </h2>
  <ul class='blogs_entrylist'>
    <li>
      <h3>
        <?php echo $this->blog->getTitle() ?>
      </h3>
      <div class="blog_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
        <?php echo $this->timestamp($this->blog->creation_date) ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->category->category_name ?></a> <?php endif; ?>
        <?php if (count($this->blogTags )):?>
        -
          <?php foreach ($this->blogTags as $tag): ?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="blog_entrylist_entry_body">
        <?php echo $this->blog->body ?>
      </div>
    </li>
  </ul>
  <?php echo $this->action("list", "comment", "core", array("type"=>"blog", "id"=>$this->blog->getIdentity())) ?>
</div>
