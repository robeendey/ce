<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _browseUsers.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<h3>
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
</h3>

<ul id="browsemembers_ul">
  <?php foreach( $this->users as $user ): ?>
    <li>
      <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
      <?php if( $this->viewer()->getIdentity() ): ?>
        <div class='browsemembers_results_links'>
          <?php echo $this->userFriendship($user) ?>
        </div>
      <?php endif; ?>

        <div class='browsemembers_results_info'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
          <?php echo $user->status; ?>
          <?php if( $user->status != "" ): ?>
            <div>
              <?php echo $this->timestamp($user->status_date) ?>
            </div>
          <?php endif; ?>
        </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php $pages = $this->users->getPages(); ?>
<?php if( $pages->current < $pages->last ): ?>
  <div class='browsemembers_loading' id="browsemembers_loading" style="display:none;">
    <?php echo $this->htmlImage('application/modules/Core/externals/images/loading.gif', $this->translate('Loading...'), array('style' => 'float:left;margin-right: 5px;')) ?>
    <?php echo $this->translate('Loading...') ?>
  </div>
  <div class='browsemembers_viewmore' id="browsemembers_viewmore">
    <a id="more_link" class="buttonlink icon_viewmore" href="javascript:browseMembersViewMore();"><?php echo $this->translate('View More');?></a>
  </div>
<?php endif; ?>

<script type="text/javascript">
  page = '<?php echo sprintf('%d', $this->page) ?>';
  totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
  userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
</script>