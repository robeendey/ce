<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7374 2010-09-14 05:02:38Z john $
 * @author     Jung
 */
?>

<ul class="classifieds_profile_tab">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='classifieds_profile_tab_photo'>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
      </div>
      <div class='classifieds_profile_tab_info'>
        <div class='classifieds_profile_tab_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          <?php if( $item->closed ): ?>
            <img src='application/modules/Classified/externals/images/close.png'/>
          <?php endif;?>
        </div>
        <div class='classifieds_browse_info_date'>
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
        </div>
        <div class='classifieds_browse_info_blurb'>
          <?php
            // Not mbstring compat
            echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo $this->translate("...");
          ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'classified_general', true), $this->translate('View All Listings'), array('class' => 'buttonlink item_icon_classified')) ?>
<?php endif;?>