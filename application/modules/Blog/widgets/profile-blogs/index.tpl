<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>

<ul class="blogs_browse">
  <?php foreach ($this->paginator as $item): ?>
    <li>
      <div class='blogs_browse_info'>
        <p class='blogs_browse_info_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </p>
        <p class='blogs_browse_info_date'>
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->creation_date) ?>
        </p>
        <p class='blogs_browse_info_blurb'>
          <?php echo strip_tags(substr($item->body, 0, 350)); if (strlen($item->body)>349) echo $this->translate("..."); ?>
        </p>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
  <?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'blog_view'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
<?php endif;?>