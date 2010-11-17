<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7305 2010-09-07 06:49:55Z john $
 * @author     Jung
 */
?>

<ul class="videos_browse">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class="video_thumb_wrapper">
        <?php if ($item->duration):?>
        <span class="video_length">
          <?php
            if( $item->duration>360 ) $duration = gmdate("H:i:s", $item->duration); else $duration = gmdate("i:s", $item->duration);
            if ($duration[0] =='0') $duration = substr($duration,1); echo $duration;
          ?>
        </span>
        <?php endif;?>
        <?php
          if ($item->photo_id) echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
          else echo '<img alt="" src="application/modules/Video/externals/images/video.png">';
        ?>
      </div>
      <a class="video_title" href='<?php echo $item->getHref();?>'><?php echo $item->getTitle();?></a>
      <div class="video_author"><?php echo $this->translate('By');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?></div>
      <div class="video_stats">
        <span class="video_views"><?php echo $item->view_count;?> <?php echo $this->translate('views');?></span>
        <?php if($item->rating>0):?>
          <?php for($x=1; $x<=$item->rating; $x++): ?><span class="rating_star_generic rating_star"></span><?php endfor; ?><?php if((round($item->rating)-$item->rating)>0):?><span class="rating_star_generic rating_star_half"></span><?php endif; ?>
        <?php endif; ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if($this->paginator->getTotalItemCount() > $this->items_per_page):?>
<br/>
<?php
  echo $this->htmlLink(array('route' => 'default', 'module' => 'video', 'controller' => 'index', 'action' => 'browse', 'user' => $item->owner_id), $this->translate('View All Videos'), array(
    'class' => 'buttonlink item_icon_video'
  ));
?>
<?php endif;?>