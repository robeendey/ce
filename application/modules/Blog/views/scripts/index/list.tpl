<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: list.tpl 7548 2010-10-05 00:24:54Z shaun $
 * @author     Jung
 */
?>

<script type="text/javascript">
  var pageAction = function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var categoryAction = function(category){
    $('page').value = 1;
    $('category').value = category;
    $('filter_form').submit();
  }
  var tagAction = function(tag){
    $('page').value = 1;
    $('tag').value = tag;
    $('filter_form').submit();
  }
  var dateAction = function(start_date, end_date){
    $('page').value = 1;
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

    <h4><?php echo $this->translate('Search Blogs') ?></h4>
    
    <form id='filter_form' class='global_form_box' method='POST'>
      <input type='text' id="search" name="search" value="<?php if( $this->search ) echo $this->search; ?>"/>
      <input type="hidden" id="tag" name="tag" value="<?php if( $this->tag ) echo $this->tag; ?>"/>
      <input type="hidden" id="category" name="category" value="<?php if( $this->category ) echo $this->category; ?>"/>
      <input type="hidden" id="page" name="page" value="<?php if( $this->page ) echo $this->page; ?>"/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if( $this->start_date) echo $this->start_date; ?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if( $this->end_date) echo $this->end_date; ?>"/>
    </form>

    <?php if( count($this->userCategories) ): ?>
      <h4><?php echo $this->translate('Categories');?></h4>
      <ul>
          <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(0);' <?php if ($this->category==0) echo " style='font-weight: bold;'";?>><?php echo $this->translate('All Categories');?></a></li>
          <?php foreach ($this->userCategories as $category): ?>
            <li><a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);' <?php if( $this->category == $category->category_id ) echo " style='font-weight: bold;'";?>>
                  <?php echo $category->category_name?>
                </a>
            </li>
          <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if( count($this->userTags) ):?>

      <h4><?php echo $this->translate('%1$s\'s Tags', $this->owner->getTitle()); ?></h4>
      <ul>
        <?php foreach ($this->userTags as $tag): ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' <?php if ($this->tag==$tag->tag_id) echo " style='font-weight: bold;'";?>>#<?php echo $tag->text?></a>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if( count($this->archive_list) ):?>
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
  <h2>
    <?php echo $this->translate('%1$s\'s Blogs', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </h2>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class='blogs_entrylist'>
    <?php foreach ($this->paginator as $item): ?>
      <li>
        <span>
          <h3>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </h3>
          <div class="blog_entrylist_entry_date">
           <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle()) ?>
            <?php echo $this->timestamp($item->creation_date) ?>
          </div>
          <div class="blog_entrylist_entry_body">
            <?php echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo "..."; ?>
          </div>
          <?php if ($item->comment_count > 0) :?>
            <?php echo $this->htmlLink($item->getHref(), $item->comment_count . ' ' . ( $item->comment_count != 1 ? 'comments' : 'comment' ), array('class' => 'buttonlink icon_comments')) ?>
          <?php endif; ?>
        </span>
      </li>
    <?php endforeach; ?>
    </ul>

  <?php elseif( $this->category || $this->tag ): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
      </span>
    </div>

  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, array("pagination/blogpagination.tpl","blog"), array("orderby"=>$this->orderby)); ?>

</div>