<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 7513 2010-10-01 01:10:18Z john $
 * @author		 John
 */
?>

<h2>
  <?php echo $this->group->__toString() ?>
  <?php echo $this->translate('&#187; Discussions');?>
</h2>

<br />

<h3>
  <?php echo $this->topic->getTitle() ?>
</h3>

<?php $this->placeholder('grouptopicnavi')->captureStart(); ?>
<div class="group_discussions_thread_options">
  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'topic', 'action' => 'index', 'group_id' => $this->group->getIdentity()), $this->translate('Back to Topics'), array(
    'class' => 'buttonlink icon_back'
  )) ?>
  <?php if( $this->canPost ): ?>
    <?php echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
      'class' => 'buttonlink icon_group_post_reply'
    )) ?>
  <?php endif; ?>
  <?php if( $this->viewer->getIdentity() ): ?>
    <?php if( !$this->isWatching ): ?>
      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
        'class' => 'buttonlink icon_group_topic_watch'
      )) ?>
    <?php else: ?>
      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
        'class' => 'buttonlink icon_group_topic_unwatch'
      )) ?>
    <?php endif; ?>
  <?php endif; ?>
  <?php if( $this->group->isOwner($this->viewer()) ): ?>
    <?php if( !$this->topic->sticky ): ?>
      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
        'class' => 'buttonlink icon_group_post_stick'
      )) ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
        'class' => 'buttonlink icon_group_post_unstick'
      )) ?>
    <?php endif; ?>
    <?php if( !$this->topic->closed ): ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
        'class' => 'buttonlink icon_group_post_close'
      )) ?>
    <?php else: ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
        'class' => 'buttonlink icon_group_post_open'
      )) ?>
    <?php endif; ?>
    <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
      'class' => 'buttonlink smoothbox icon_group_post_rename'
    )) ?>
    <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
      'class' => 'buttonlink smoothbox icon_group_post_delete'
    )) ?>
  <?php elseif( $this->group->isOwner($this->viewer()) == false): ?>
    <?php if( $this->topic->closed ): ?>
      <div class="group_discussions_thread_options_closed">
        <?php echo $this->translate('This topic has been closed.');?>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
<?php $this->placeholder('grouptopicnavi')->captureEnd(); ?>



<?php echo $this->placeholder('grouptopicnavi') ?>
<?php echo $this->paginationControl(null, null, null, array(
  'params' => array(
    'post_id' => null // Remove post id
  )
)) ?>


<script type="text/javascript">
  var quotePost = function(user, href, body) {
    if( $type(body) == 'element' ) {
      body = $(body).getParent('li').getElement('.group_discussions_thread_body_raw').get('html').trim();
    }
    $("body").value = '[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n';
    $("body").focus();
    $("body").scrollTo(0, $("body").getScrollSize().y);
  }
</script>



<ul class='group_discussions_thread'>
  <?php foreach( $this->paginator as $post ): ?>
  <li>
    <div class="group_discussions_thread_photo">
      <?php
        $user = $this->item('user', $post->user_id);
        echo $this->htmlLink($user->getHref(), $user->getTitle());
        echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));
      ?>
    </div>
    <div class="group_discussions_thread_info">
      <div class="group_discussions_thread_details">
        <div class="group_discussions_thread_details_options">
          <?php if( $this->form ): ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Quote'), array(
              'class' => 'buttonlink icon_group_post_quote',
              'onclick' => 'quotePost("'.$this->escape($user->getTitle()).'", "'.$this->escape($user->getHref()).'", this);',
            )) ?>
          <?php endif; ?>
          <?php if( $post->user_id == $this->viewer()->getIdentity() || $this->group->getOwner()->getIdentity() == $this->viewer()->getIdentity() ): ?>
            <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
              'class' => 'buttonlink smoothbox icon_group_post_edit'
            )) ?>
            <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
              'class' => 'buttonlink smoothbox icon_group_post_delete'
            )) ?>
          <?php endif; ?>
        </div>
        <div class="group_discussions_thread_details_date">
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
        </div>
      </div>
      <div class="group_discussions_thread_body">
        <?php echo nl2br($this->BBCode($post->body)) ?>
      </div>
      <span class="group_discussions_thread_body_raw" style="display: none;">
        <?php echo $post->body; ?>
      </span>
    </div>
  </li>
  <?php endforeach; ?>
</ul>


<?php if($this->paginator->getCurrentItemCount() > 4): ?>

  <?php echo $this->paginationControl(null, null, null, array(
    'params' => array(
      'post_id' => null // Remove post id
    )
  )) ?>
  <br />
  <?php echo $this->placeholder('grouptopicnavi') ?>

<?php endif; ?>

<br />

<?php if( $this->form ): ?>
  <a name="reply" />
  <?php echo $this->form->setAttrib('id', 'group_topic_reply')->render($this) ?>
<?php endif; ?>