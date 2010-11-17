<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: network.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Alex
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js');
?>

<script type="text/javascript">
  function leaveNetwork(network_id)
  {
    $('leave_id').value = network_id;
    $('network-form').submit();
  }

  en4.core.runonce.add(function()
  {
    var networkAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'network', 'controller' => 'network', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'tokenValueKey': 'title',
      'minLength': 0,
      'selectMode': 'pick',
      'selectFirst' : true,
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true
    });

    $('title').addEvent('keypress', function() {
      $('title').removeClass('network_join_selected');
      $('join_id').value = '';
    });

    networkAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      //console.log([element, selected, value, input]);
      var network_id = selected.retrieve('autocompleteChoice').id;
      $('title').addClass('network_join_selected');
      $('join_id').value = network_id;
    });
  });
</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('My Settings');?>
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

<div class='layout_middle'>
<h3><?php echo $this->translate('My Networks');?></h3>
<p>
  <?php echo $this->translate(array('You belong to %s network.', 'You belong to %s networks.', count($this->networks)),$this->locale()->toNumber(count($this->networks))) ?>
</p>

<ul class='networks'>
<?php foreach ($this->networks as $network): ?>
  <li>
    <div>
      <?php echo $network->title ?> <span>(<?php echo $this->translate(array('%s member.', '%s members.', $network->membership()->getMemberCount()),$this->locale()->toNumber($network->membership()->getMemberCount())) ?>)</span>
    </div>
    <?php if( $network->assignment == 0 ): ?>
      <a href='javascript:void(0);' onclick="leaveNetwork(<?php echo $network->network_id;?>)"><?php echo $this->translate('Leave Network');?></a>
    <?php endif; ?>
  </li>
<?php endforeach; ?>
</ul>

<p>
  <?php echo $this->translate('To add a new network, begin typing its name below.');?>
</p>

<br />

<?php echo $this->form->render($this) ?>
</div>










