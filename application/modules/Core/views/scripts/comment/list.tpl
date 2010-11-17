<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: list.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php if( !$this->page ): ?>
<div class='comments' id="comments">
<?php endif; ?>
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>

    <?php if( isset($this->form) ): ?>
      - <a href='javascript:void(0);' onclick="$('comment-form').style.display = '';$('comment-form').body.focus();"><?php echo $this->translate('Post Comment') ?></a>
    <?php endif; ?>

    <?php if( $this->viewer()->getIdentity() ): ?>
      <?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>
        - <a href="javascript:void(0);" onclick="en4.core.comments.unlike('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" onclick="en4.core.comments.like('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <ul>
    
    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);', 
                          $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('onclick' => 'en4.core.comments.showLikes("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'");')
                      ); ?>
          </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
              'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page - 1).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
              'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if( $this->page ):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for( ; $i != $e; $i += $d ):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
        ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php echo $this->htmlLink($poster->getHref(),
              $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
            ) ?>
          </div>
          <div class="comments_info">
            <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?></span>
            <?php echo $this->viewMore($comment->body) ?>
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
            </div>
            <?php if( $canDelete ): ?>
              <div class="comments_comment_options">
                <?php /* echo $this->htmlLink(array(
                  'reset' => true, 'route' => 'default', 'controller' => 'comment',
                  'action' => 'delete', 'comment_id' => $comment->comment_id,
                  'type' => $this->subject()->getType(),
                  'id' => $this->subject()->getIdentity(),),
                  'delete', array(
                    'class' => 'smoothbox',
                  )
                ) */ ?>
                <a href="javascript:void(0);" onclick="en4.core.comments.deleteComment('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
                  <?php echo $this->translate('delete') ?>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page + 1).'")'
            )) ?>
          </div>
        </li>
      <?php endif; ?>

    <?php endif; ?>

  </ul>
  <script type="text/javascript">
    en4.core.runonce.add(function(){
      $($('comment-form').body).autogrow();
      en4.core.comments.attachCreateComment($('comment-form'));
    });
  </script>
  <?php if( isset($this->form) ) echo $this->form->setAttribs(array('id' => 'comment-form', 'style' => 'display:none;'))->render() ?>
<?php if( !$this->page ): ?>
</div>
    <?php endif; ?>