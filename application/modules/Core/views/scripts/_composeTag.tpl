<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composeTag.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php $this->headScript()
    ->appendFile($this->baseUrl().'/externals/autocompleter/Observer.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->baseUrl().'/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile('application/modules/Core/externals/scripts/composer_tag.js') ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Tag({
      suggestOptions : {
        'url' : '<?php echo $this->baseUrl().'/index.php/user/friends/suggest' ?>',
        'data' : {
          'format' : 'json'
        }
      },
      'suggestProto' : 'local',
      'suggestParam' : <?php echo $this->action('suggest', 'friends', 'user', array('sendNow' => false, 'includeSelf' => true)) ?>
    }));
  });
</script>