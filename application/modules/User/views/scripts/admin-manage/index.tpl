<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7606 2010-10-08 00:15:43Z bryan $
 * @author     John
 */
?>

<h2>
  <?php echo $this->translate("Manage Members") ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("USER_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }

function multiModify()
{
  var multimodify_form = $('multimodify_form');
  if (multimodify_form.submit_button.value == 'delete')
  {
    return confirm('<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected user accounts?")) ?>');
  }
}

function selectAll()
{
  var i;
  var multimodify_form = $('multimodify_form');
  var inputs = multimodify_form.elements;
  for (i = 1; i < inputs.length - 1; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}

function loginAsUser(id) {
  if( !confirm('<?php echo $this->translate('Note that you will be logged out of your current account if you click ok.') ?>') ) {
    return;
  }
  var url = '<?php echo $this->url(array('action' => 'login')) ?>';
  var baseUrl = '<?php echo $this->url(array(), 'default', true) ?>';
  (new Request.JSON({
    url : url,
    data : {
      format : 'json',
      id : id
    },
    onSuccess : function() {
      window.location.replace( baseUrl );
    }
  })).send();
}
</script>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<br />

<div class='admin_results'>
  <div>
    <?php $memberCount = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s member found", "%s members found", $memberCount), ($memberCount)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
</div>

<br />

<div class="admin_table_form">
<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th style='width: 1%;'><input onclick="selectAll()" type='checkbox' class='checkbox'></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><a href="javascript:void(0);" onclick="javascript:changeOrder('level_id', 'ASC');"><?php echo $this->translate("User Level") ?></a></th>
        <th style='width: 1%;' class='admin_table_centered'><?php echo $this->translate("Approved") ?></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'DESC');"><?php echo $this->translate("Signup Date") ?></a></th>
        <th style='width: 1%;' class='admin_table_options'><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ): ?>
          <tr>
            <td><input <?php if ($item->level_id == 1) echo 'disabled';?> name='modify_<?php echo $item->getIdentity();?>' value=<?php echo $item->getIdentity();?> type='checkbox' class='checkbox'></td>
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'>
              <?php
                $display_name = $this->item('user', $item->user_id)->getTitle();
                $display_name = Engine_String::strlen($display_name) > 10 ? Engine_String::substr($display_name, 0, 10) . '...' : $display_name;
                echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $display_name, array('target' => '_blank'))
              ?>
            </td>
            <td class='admin_table_user'><?php echo $this->htmlLink($this->item('user', $item->user_id)->getHref(), $this->item('user', $item->user_id)->username, array('target' => '_blank')) ?></td>
            <td class='admin_table_email'>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class='admin_table_centered'>
              <a href='<?php echo $this->url(array('module'=>'authorization','controller'=>'level', 'action' => 'edit', 'id' => $item->level_id)) ?>'>
                <?php echo $this->translate(Engine_Api::_()->getItem('authorization_level', $item->level_id)->getTitle()) ?>
              </a>
            </td>
            <td class='admin_table_centered'><?php echo ( $item->enabled ? $this->translate('Yes') : $this->translate('No') ) ?></td>
            <td><?php echo $item->creation_date ?></td>
            <td class='admin_table_options'>
              <a class='smoothbox' href='<?php echo $this->url(array('action' => 'edit', 'id' => $item->user_id));?>'>
                <?php echo $this->translate("edit") ?>
              </a>
              <?php if (($this->superAdminCount>1 && $item->level_id==1) || $item->level_id != 1): // @todo change this to look up actual superadmin level ?>
                |
                <a class='smoothbox' href='<?php echo $this->url(array('action' => 'delete', 'id' => $item->user_id));?>'>
                  <?php echo $this->translate("delete") ?>
                </a>
              <?php endif;?>
              <?php if( $item->level_id != 1 ): // @todo change this to look up actual superadmin level ?>
                |
                <a href='<?php echo $this->url(array('action' => 'login', 'id' => $item->user_id));?>' onclick="loginAsUser(<?php echo $item->user_id ?>); return false;">
                  <?php echo $this->translate("login") ?>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='submit' name="submit_button" value="approve"><?php echo $this->translate("Approve Selected") ?></button>
    <button type='submit' name="submit_button" value="delete"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
</form>
</div>