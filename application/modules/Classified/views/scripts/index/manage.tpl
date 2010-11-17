<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manage.tpl 7374 2010-09-14 05:02:38Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }

  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
        if (f.value == '' && f.id.match(/\min$/)) {
            new OverText(f, {'textOverride':'min','element':'span'});
            //f.set('class', 'integer_field_unselected');
        }
        if (f.value == '' && f.id.match(/\max$/)) {
            new OverText(f, {'textOverride':'max','element':'span'});
            //f.set('class', 'integer_field_unselected');
        }
    });
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Classified Listings');?>
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

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
  <?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You have already created the maximum number of listings allowed. If you would like to create a new listing, please delete an old one first.');?>
      </span>
    </div>
    <br/>
  <?php endif; ?>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class="classifieds_browse">
      <?php foreach( $this->paginator as $item ): ?>
        <li>
          <div class='classifieds_browse_photo'>
            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
          </div>
          <div class='classifieds_browse_options'>
            <?php echo $this->htmlLink(array(
              'route' => 'classified_specific',
              'action' => 'edit',
              'classified_id' => $item->getIdentity(),
            ), $this->translate('Edit Listing'), array(
              'class' => 'buttonlink icon_classified_edit'
            )) ?>
            <?php if( $this->allowed_upload ): ?>
              <?php echo $this->htmlLink(array(
                  'route' => 'classified_extended',
                  'controller' => 'photo',
                  'action' => 'upload',
                  'subject' => $item->getGuid(),
                ), $this->translate('Add Photos'), array(
                  'class' => 'buttonlink icon_classified_photo_new'
              )) ?>
            <?php endif; ?>

            <?php if( !$item->closed ): ?>
              <?php echo $this->htmlLink(array(
                'route' => 'classified_specific',
                'action' => 'close',
                'classified_id' => $item->getIdentity(),
                'closed' => 1,
              ), $this->translate('Close Listing'), array(
                'class' => 'buttonlink icon_classified_close'
              )) ?>
            <?php else: ?>
              <?php echo $this->htmlLink(array(
                'route' => 'classified_specific',
                'action' => 'close',
                'classified_id' => $item->getIdentity(),
                'closed' => 0,
              ), $this->translate('Open Listing'), array(
                'class' => 'buttonlink icon_classified_open'
              )) ?>
            <?php endif; ?>

            <?php echo $this->htmlLink(array(
              'route' => 'classified_specific',
              'action' => 'delete',
              'classified_id' => $item->getIdentity(),
            ), $this->translate('Delete Listing'), array(
              'class' => 'buttonlink icon_classified_delete'
            )) ?>
          </div>
          <div class='classifieds_browse_info'>
            <div class='classifieds_browse_info_title'>
              <h3>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                <?php if( $item->closed ): ?>
                  <img alt="close" src='application/modules/Classified/externals/images/close.png'/>
                <?php endif;?>
              </h3>
            </div>
            <div class='classifieds_browse_info_date'>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              -
              <?php echo $this->translate('posted by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </div>
            <div class='classifieds_browse_info_blurb'>
              <?php $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($item)?>
              <?php echo $this->fieldValueLoop($item, $fieldStructure) ?>
              <?php
                // Not mbstring compat
                echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo "...";
              ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any classified listing that match your search criteria.');?>
      </span>
    </div>
  <?php else:?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any classified listings.');?>
        <?php if ($this->can_create): ?>
          <?php echo $this->translate('Get started by <a href=\'%1$s\'>posting</a> a new listing.', $this->url(array('action' => 'create'), 'classified_general'));?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl","classified")); ?>
</div>
