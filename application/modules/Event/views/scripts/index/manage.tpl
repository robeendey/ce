<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manage.tpl 7422 2010-09-20 03:18:32Z john $
 * @author     Sami
 */
?>

<div class="headline">
  <h2>
    <?php echo $this->translate('Events');?>
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

<?php if( count($this->paginator) > 0 ): ?>

  <div class='layout_right'>
    <?php echo $this->formFilter->setAttrib('class', 'filters')->render($this) ?>
    <br />
    <div class="quicklinks">
      <ul>
        <li>
          <?php echo $this->htmlLink(array('route' => 'event_general', 'action' => 'create'), $this->translate('Create New Event'), array(
            'class' => 'buttonlink icon_event_create'
          )) ?>
        </li>
      </ul>
    </div>
  </div>

  <div class='layout_middle'>
    <ul class='events_browse'>
      <?php foreach( $this->paginator as $event ): ?>
        <li>
          <div class="events_photo">
            <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
          </div>
          <div class="events_options">
            <?php if( $this->viewer() && $event->isOwner($this->viewer()) ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'edit', 'event_id' => $event->getIdentity()), $this->translate('Edit Event'), array(
                'class' => 'buttonlink icon_event_edit'
              )) ?>
            <?php endif; ?>
            <?php if( $this->viewer() && !$event->membership()->isMember($this->viewer(), null) ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
                'class' => 'buttonlink smoothbox icon_event_join'
              )) ?>
              <?php elseif( $this->viewer() && $event->isOwner($this->viewer()) ): ?>
                <?php echo $this->htmlLink(array('route' => 'event_specific', 'action' => 'delete', 'event_id' => $event->getIdentity()), $this->translate('Delete Event'), array(
                  'class' => 'buttonlink REMsmoothbox icon_event_delete'
                )) ?>
            <?php elseif( $this->viewer() && $event->membership()->isMember($this->viewer()) ): ?>
              <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
                'class' => 'buttonlink smoothbox icon_event_leave'
              )) ?>
            <?php endif; ?>
          </div>
          <div class="events_info">
            <div class="events_title">
              <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
            </div>
	    <div class="events_members">
	      <?php echo $this->locale()->toDateTime($event->starttime) ?>
	    </div>
            <div class="events_members">
              <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by') ?>
              <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
            </div>
            <div class="events_desc">
              <?php echo $event->getDescription() ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php if( $this->paginator->count() > 1 ): ?>
      <?php echo $this->paginationControl($this->paginator, null, null, array(
        'query' => array('view'=>$this->view, 'text'=>$this->text)
      )); ?>
    <?php endif; ?>
    
  </div>
<?php else: ?>
  <div class="tip">
    <span>
       <?php echo $this->translate('Tip: %1$sClick here%2$s to create a event or %3$sbrowse%2$s for events to join!', "<a href='".$this->url(array('action' => 'create'), 'event_general', true)."'>", '</a>', "<a href='".$this->url(array(), 'event_upcoming', true)."'>"); ?>
    </span>
  </div>
<?php endif; ?>


