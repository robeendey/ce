<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @access	   John
 */
?>

<ul class="events_profile_tab">
  <?php foreach( $this->paginator as $event ): ?>
    <li>
      <div class="events_profile_tab_photo">
        <?php echo $this->htmlLink($event, $this->itemPhoto($event, 'thumb.normal')) ?>
      </div>
      <div class="events_profile_tab_info">
        <div class="events_profile_tab_title">
          <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
        </div>
        <div class="events_profile_tab_members">
          <?php echo $this->translate(array('%s guest', '%s guests', $event->member_count),$this->locale()->toNumber($event->member_count)) ?>
        </div>
        <div class="events_profile_tab_desc">
          <?php echo $event->getDescription() ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if(true):?>
  <br/>
  <?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'event_general'), $this->translate('View All Events'), array('class' => 'buttonlink item_icon_event')) ?>
<?php endif;?>