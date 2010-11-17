
<?php $user = $this->subject; ?>

<ul class="forum_topic_posts">
  <?php foreach( $this->paginator as $post ):
    if( !isset($signature) ) $signature = $post->getSignature();
    $topic = $post->getParent();
    $forum = $topic->getParent();
    ?>
    <li>
      <div class="forum_topic_posts_info">
        <div class="forum_topic_posts_info_top">
          <div class="forum_topic_posts_info_top_date">
            <?php echo $this->locale()->toDateTime(strtotime($post->creation_date));?>
          </div>
          <div class="forum_topic_posts_info_top_parents">
            <?php echo $this->translate('in the topic %1$s', $topic->__toString()) ?>
            <?php echo $this->translate('in the forum %1$s', $forum->__toString()) ?>
          </div>
        </div>
        <div class="forum_topic_posts_info_body">
          <?php if( $this->decode_bbcode ) {
            echo nl2br($this->BBCode($post->body));
          } else {
            echo $post->body;
          } ?>
          <?php if( $post->edit_id ): ?>
            <i>
              <?php echo $this->translate('This post was edited by %1$s at %2$s', $this->user($post->edit_id)->__toString(), $this->locale()->toDateTime(strtotime($post->creation_date))); ?>
            </i>
          <?php endif;?>
        </div>
        <?php if( $post->file_id ): ?>
          <div class="forum_topic_posts_info_photo">
            <?php echo $this->itemPhoto($post, null, '', array('class'=>'forum_post_photo'));?>
          </div>
        <?php endif;?>
      </div>
    </li>
  <?php endforeach;?>
</ul>
