<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Poll
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7443 2010-09-22 07:25:41Z john $
 * @author     Steve
 */
?>

<ul class="polls_browse">
  <?php foreach ($this->paginator as $item): ?>
    <li>
      <div class='polls_browse_info'>
        <?php echo $this->htmlLink(array('route'=>'poll_view', 'user_id'=>$item->user_id, 'poll_id'=>$item->poll_id), $item->getTitle()) ?>
        <div class='polls_browse_info_date'>
          <?php echo $this->translate('Posted') ?> <?php echo $this->timestamp($item->creation_date) ?>
        </div>
        <div class='polls_browse_info_desc'>
          <?php echo $item->description ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
<?php if($this->paginator->getTotalItemCount() > $this->items_per_page): ?>
  <?php echo $this->htmlLink(
    $this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'poll_general', true),
    $this->translate('View All Polls'),
    array('class' => 'buttonlink item_icon_poll')
  ) ?>
<?php endif;?>