<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: account.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<script type="text/javascript">
//<![CDATA[
  window.addEvent('load', function()
  {
    $('profile_address').innerHTML = $('profile_address').innerHTML.replace('<?php echo $this->translate('yourname');?>', '<span id="profile_address_text"><?php echo $this->translate('yourname');?></span>');

    $('username').addEvent('keyup', function()
    {
      var text = '<?php echo $this->translate('yourname');?>';
      if( this.value != '' )
      {
        text = this.value;
      }
      $('profile_address_text').innerHTML = text;
    });
    // trigger on page-load
    if ($('username').value.length)
        $('username').fireEvent('keyup');
  });
//]]>
</script>

<?php echo $this->form->render($this) ?>
