<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 7374 2010-09-14 05:02:38Z john $
 * @author     Jung
 */
?>

<?php if( !$this->classified): ?>
<?php echo $this->translate('The classified you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<script type="text/javascript">
  var categoryAction =function(category){
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
  <div class='classifieds_gutter'>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner), array('class' => 'classifieds_gutter_photo')) ?>
    <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle(), array('class' => 'classifieds_gutter_name')) ?>
    
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->gutterNavigation)
        ->setUlClass('navigation classifieds_gutter_options')
        ->render();
    ?>

    <?php /*
    <ul class='classifieds_gutter_options'>
      <?php if ($this->classified->owner_id == $this->viewer->getIdentity()||$this->can_edit):?>
        <li>
          <?php echo $this->htmlLink(array(
            'route' => 'classified_specific',
            'action' => 'edit',
            'classified_id' => $this->classified->classified_id
          ), $this->translate('Edit This Listing'), array(
            'class' => 'buttonlink icon_classified_edit'
          )) ?>
        </li>
        <?php if( $this->allowed_upload ): ?>
        <li>
          <?php echo $this->htmlLink(array(
              'route' => 'classified_extended',
              'controller' => 'photo',
              'action' => 'upload',
              'subject' => $this->classified->getGuid(),
            ), $this->translate('Add Photos'), array(
              'class' => 'buttonlink icon_classified_photo_new'
          )) ?>
        </li>
        <?php endif; ?>
        <li>
          <?php if( !$this->classified->closed ): ?>
            <?php echo $this->htmlLink(array(
              'route' => 'classified_specific',
              'action' => 'close',
              'classified_id' => $this->classified->classified_id,
              'closed' => 1,
            ), $this->translate('Close Listing'), array(
              'class' => 'buttonlink icon_classified_close'
            )) ?>
          <?php else: ?>
            <?php echo $this->htmlLink(array(
              'route' => 'classified_specific',
              'action' => 'close',
              'classified_id' => $this->classified->classified_id,
              'closed' => 0,
            ), $this->translate('Open Listing'), array(
              'class' => 'buttonlink icon_classified_open'
            )) ?>
          <?php endif; ?>
        </li>
        <li>
          <?php echo $this->htmlLink(array(
            'route' => 'classified_specific',
            'action' => 'delete',
            'classified_id' => $this->classified->classified_id,
          ), $this->translate('Delete Listing'), array(
            'class' => 'buttonlink icon_classified_delete'
          )) ?>
        </li>
      <?php endif; ?>
    </ul>
     *
     */ ?>

    <form id='filter_form' class='global_form_box' method='post' action='<?php echo $this->url(array('module' => 'classified', 'controller' => 'index', 'action' => 'index'), 'default', true) ?>' style='display: none;'>
      <input type="hidden" id="tag" name="tag" value=""/>
      <input type="hidden" id="category" name="category" value=""/>
      <input type="hidden" id="start_date" name="start_date" value="<?php if ($this->start_date) echo $this->start_date;?>"/>
      <input type="hidden" id="end_date" name="end_date" value="<?php if ($this->end_date) echo $this->end_date;?>"/>
    </form>

    <?php if (count($this->userCategories )):?>
      <h4><?php echo $this->translate('Categories');?></h4>
      <ul>
          <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(0);'><?php echo $this->translate('All Categories');?></a></li>
          <?php foreach ($this->userCategories as $category): ?>
            <li> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $category->category_id?>);'><?php echo $this->translate($category->category_name) ?></a></li>
          <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php
    $this->tagstring = "";
    if (count($this->userTags )){
      foreach ($this->userTags as $tag){
        if (!empty($tag->text)){
          $this->tagstring .= " <a href='javascript:void(0);'onclick='javascript:tagAction({$tag->tag_id})' >#$tag->text</a> ";
        }
      }
    }
    ?>

    <?php if ($this->tagstring ):?>
      <h4><?php echo $this->translate('%1$s\'s Tags', $this->user($this->classified->owner_id)->getTitle())?></h4>
      <ul>
        <?php echo $this->tagstring;?>
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
  <h2>
    <?php echo $this->translate('%1$s\'s Classified Listing', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </h2>
  <ul class='classifieds_entrylist'>
    <li>
      <h3>
        <?php echo $this->classified->getTitle() ?>
      </h3>

      <?php if ($this->classified->closed == 1):?>
        <br />
        <div class="tip">
          <span>
            <?php echo $this->translate('This classified listing has been closed by the poster.');?>
          </span>
        </div>
        <br/>
      <?php endif; ?>

      <div class="classified_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->classified->getParent(), $this->classified->getParent()->getTitle()) ?>
        <?php echo $this->timestamp($this->classified->creation_date) ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->translate($this->category->category_name) ?></a> <?php endif; ?>
        <?php if (count($this->classifiedTags )):?>
        -
          <?php foreach ($this->classifiedTags as $tag): ?>
          <?php if (!empty($tag->getTag()->text)):?>
            <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
          <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php echo $this->fieldValueLoop($this->classified, $this->fieldStructure) ?>
            <?php /*
        <?php foreach ($this->fieldsByAlias as $key => $value): ?>
          <?php if($value):?>
            <?php if($key == "location"):?>
              <br/><?php echo ucfirst($key); ?>: <?php echo $value;?> [<a href='http://maps.google.com/?q=<?php echo $value;?>' target='_blank'><?php echo $this->translate('map');?></a>]&nbsp;
            <?php elseif($key == "price"):?>
              <br/><?php echo ucfirst($key); ?>:
              <?php echo Engine_Api::_()->getApi('settings', 'core')->classified_currency . $this->locale()->toCurrency($value) ?><br/>
            <?php else:?>
              <br/><?php echo ucfirst($key); ?>: <?php echo $value;?>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
             *
             */ ?>

      </div>
      <div class="classified_entrylist_entry_body">
        <?php echo nl2br($this->classified->body) ?>
      </div>
        <ul class='classified_thumbs'>
          <?php if($this->main_photo):?>
            <li>
              <div class="classifieds_thumbs_description">
                <?php if( '' != $this->main_photo->getDescription() ): ?>
                  <?php echo $this->string()->chunk($this->main_photo->getDescription(), 100) ?>
                <?php endif; ?>
              </div>
              <?php echo $this->htmlImage($this->main_photo->getPhotoUrl(), $this->main_photo->getTitle(), array(
                'id' => 'media_photo'
              )); ?>
            </li>
          <?php endif; ?>

          <?php foreach( $this->paginator as $photo ): ?>
            <?php if($this->classified->photo_id != $photo->file_id):?>
              <li>
                <div class="classifieds_thumbs_description">
                  <?php if( '' != $photo->getDescription() ): ?>
                    <?php echo $this->string()->chunk($photo->getDescription(), 100) ?>
                  <?php endif; ?>
                </div>
                <?php echo $this->htmlImage($photo->getPhotoUrl(), $photo->getTitle(), array(
                  'id' => 'media_photo'
                )); ?>
              </li>
            <?php endif; ?>
          <?php endforeach;?>
        </ul>
    </li>
  </ul>
  <?php echo $this->action("list", "comment", "core", array("type"=>"classified", "id"=>$this->classified->getIdentity())) ?>
</div>