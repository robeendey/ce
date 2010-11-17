<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: view.tpl 7513 2010-10-01 01:10:18Z john $
 * @author     John
 */
?>

<h2>
<?php echo $this->htmlLink(array('route'=>'forum_general'), $this->translate("Forums"));?>
  &#187; <?php echo $this->htmlLink(array('route'=>'forum_forum', 'forum_id'=>$this->forum->getIdentity()), $this->forum->getTitle());?>
</h2>

<div class="forum_topic_title_wrapper">
  <div class="forum_topic_title_options">
    <?php echo $this->htmlLink($this->forum->getHref(), $this->translate('Back To Topics'), array(
      'class' => 'buttonlink icon_back'
    )) ?>
    <?php if( $this->canPost ): ?>
      <?php echo $this->htmlLink($this->topic->getHref(array('action' => 'post-create')), $this->translate('Post Reply'), array(
        'class' => 'buttonlink icon_forum_post_reply'
      )) ?>
    <?php endif; ?>
    <?php if( $this->viewer->getIdentity() ): ?>
      <?php if( !$this->isWatching ): ?>
        <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
          'class' => 'buttonlink icon_forum_topic_watch'
        )) ?>
      <?php else: ?>
        <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
          'class' => 'buttonlink icon_forum_topic_unwatch'
        )) ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="forum_topic_title">
    <h3><?php echo $this->topic->getTitle() ?></h3>
  </div>
</div>

<?php if( $this->canEdit || $this->canDelete ): ?>
  <div class="forum_topic_options">

    <?php if( $this->canEdit ): ?>
      <?php if( !$this->topic->sticky ): ?>
        <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
          'class' => 'buttonlink icon_forum_post_stick'
        )) ?>
      <?php else: ?>
        <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
          'class' => 'buttonlink icon_forum_post_unstick'
        )) ?>
      <?php endif; ?>
      <?php if( !$this->topic->closed ): ?>
        <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
          'class' => 'buttonlink icon_forum_post_close'
        )) ?>
      <?php else: ?>
        <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
          'class' => 'buttonlink icon_forum_post_unclose'
        )) ?>
      <?php endif; ?>
      <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
        'class' => 'buttonlink smoothbox icon_forum_post_rename'
      )) ?>
      <?php echo $this->htmlLink(array('action' => 'move', 'reset' => false), $this->translate('Move'), array(
        'class' => 'buttonlink smoothbox icon_forum_post_move'
      )) ?>
    <?php endif; ?>
    <?php if( $this->canDelete ): ?>
      <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
        'class' => 'buttonlink smoothbox icon_forum_post_delete'
      )) ?>
    <?php endif; ?>
  </div>
<?php endif; ?>

<?php if( $this->topic->closed ): ?>
  <div class="forum_discussions_thread_options_closed">
    <?php echo $this->translate('This topic has been closed.');?>
  </div>
<?php endif; ?>

<div class="forum_topic_pages">
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<ul class="forum_topic_posts">
  <?php foreach( $this->paginator as $post ):
    if( !isset($isFirstPost) && $this->paginator->getCurrentPageNumber() <= 1 ) {
      $isFirstPost = true;
    } else {
      $isFirstPost = false;
    }
    ?>
    <?php $user = $this->user($post->user_id); ?>
    <?php $signature = $post->getSignature(); ?>
    <li>
      <div class="forum_topic_posts_author">
        <div class="forum_topic_posts_author_name">
        <?php echo $user->__toString(); ?>
        </div>
        <div class="forum_topic_posts_author_photo">
        <?php echo $this->itemPhoto($user); ?>
        </div>
        <ul class="forum_topic_posts_author_info">
          <?php if( $post->getOwner() ): ?>
            <?php if( $this->forum->isModerator($post->getOwner())): ?>
              <li class="forum_topic_posts_author_info_title"><?php echo $this->translate('Moderator');?></li>
            <?php endif;?>
          <?php endif;?>

          <?php if( $signature ): ?>
            <li>
              <?php echo $signature->post_count; ?>
              <?php echo $this->translate('posts');?>
            </li>
          <?php endif; ?>
        </ul>
      </div>
      <div class="forum_topic_posts_info">
        <div class="forum_topic_posts_info_top">
          <div class="forum_topic_posts_info_top_date">
            <?php echo $this->locale()->toDateTime(strtotime($post->creation_date));?>
          </div>
          <div class="forum_topic_posts_info_top_options">
            <?php if( $this->canPost ): ?>
              <?php echo $this->htmlLink(array(
                'route' => 'forum_topic',
                'action' => 'post-create',
                'topic_id'=>$this->subject()->getIdentity(),
                'quote_id'=>$post->getIdentity(),
              ), $this->translate('Quote'), array(
                'class' => 'buttonlink icon_forum_post_quote',
              )) ?>
            <?php endif;?>
            <?php if( $this->canEdit || ($post->isOwner($this->viewer) && !$this->topic->closed) ): ?>
              <a href="<?php echo $this->url(array('post_id'=>$post->getIdentity(), 'action'=>'edit'), 'forum_post'); ?>" class="buttonlink icon_forum_post_edit"><?php echo $this->translate('Edit');?></a>
            <?php endif;?>
            <?php if( !$isFirstPost && ($this->canEdit || ($post->isOwner($this->viewer) && !$this->topic->closed)) ): ?>
              <a href="<?php echo $this->url(array('post_id'=>$post->getIdentity(), 'action'=>'delete'), 'forum_post');?>" class="buttonlink smoothbox icon_forum_post_delete"><?php echo $this->translate('Delete');?></a>
            <?php endif;?>
          </div>
        </div>
        <div class="forum_topic_posts_info_body">
          <?php if( $this->decode_bbcode ) {
            echo nl2br($this->BBCode($post->body));
          } else {
            echo $post->body;  
          } ?>
          <?php if ($post->edit_id):?>
            <i>
              <?php echo $this->translate('This post was edited by %1$s at %2$s', $this->user($post->edit_id)->__toString(), $this->locale()->toDateTime(strtotime($post->creation_date))); ?>
            </i>
          <?php endif;?>
        </div>
        <?php if ($post->file_id):?>
          <div class="forum_topic_posts_info_photo">
            <?php echo $this->itemPhoto($post, null, '', array('class'=>'forum_post_photo'));?>
          </div>
        <?php endif;?>
      </div>
    </li>
  <?php endforeach;?>
</ul>

<div class="forum_topic_pages">
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php if( $this->canPost && $this->form ): ?>
  <?php echo $this->form->render(); ?>
<?php endif; ?>