<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: field-create.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<?php if( $this->form ): ?>

  <?php
    $this->headScript()
      ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
      ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
      ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js');
  ?>

  <script type="text/javascript">
    var linkUrl = '<?php echo $this->url(array('action' => 'map-create')) ?>';
    var tokens = <?php echo Zend_Json::encode(array_values($this->fieldData)) ?>;
    var au;
    window.addEvent('domready', function(){
      //var tokens = <?php echo $this->friends ?>;
      au = new Autocompleter.Local('label', tokens, {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'selection',
        'multiple': false,
        'className': 'field-autosuggest',
        'filterSubset' : true,
        'customChoices' : true,
        'tokenFormat' : 'string',
        'injectChoice': function(token){
          var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token, 'id':token});
          new Element('div', {'html': this.markQueryValue(token),'class': 'autocompleter-choice'}).inject(choice);
          choice.inputValue = token;
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        },
        'onSelection' : function(element, selected, value, input) {
          var form = $$('.global_form_smoothbox')[0];
          form.action = (new URI(linkUrl).setData({'label' : value}, true)).toString();
          form.submit();
        }
      });
    });

  </script>


  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <div>
    <?php echo $this->translate("Changes saved.") ?>
  </div>

  <script type="text/javascript">
    parent.onFieldCreate(
      <?php echo Zend_Json::encode($this->field) ?>,
      <?php echo Zend_Json::encode($this->htmlArr) ?>
    );
    (function() { parent.Smoothbox.close(); }).delay(1000);
  </script>

<?php endif; ?>