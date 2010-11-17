<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Group
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7250 2010-09-01 07:42:35Z john $
 * @author		 John
 */
?>

<ul>
  <?php foreach( $this->paginator as $group ): ?>
    <li>
      <div class="groups_profile_tab_photo">
        <?php echo $this->htmlLink($group, $this->itemPhoto($group, 'thumb.normal')) ?>
      </div>
      <div class="groups_profile_tab_info">
        <div class="groups_profile_tab_title">
          <?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
        </div>
        <div class="groups_profile_tab_members">
          <?php echo $this->translate(array('%s member', '%s members', $group->member_count),$this->locale()->toNumber($group->member_count)) ?>
        </div>
        <div class="groups_profile_tab_desc">
          <?php echo $group->getDescription() ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php if(true):?>
  <br/>
  <?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'group_general'), $this->translate('View All Groups'), array('class' => 'buttonlink item_icon_group')) ?>
<?php endif;?>