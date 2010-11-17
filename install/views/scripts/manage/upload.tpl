<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: upload.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php if( $this->error ): ?>
  <?php //echo $this->translate($this->error) ?>
  <?php echo $this->error ?> <br />
<?php else: ?>
  Uploaded successfully! <br />
<?php endif; ?>

<a href="<?php echo $this->url(array('action' => 'select')) ?>">Return to Choose Packages</a> <br />
<a href="<?php echo $this->url(array('action' => 'index')) ?>">Return to Manager</a> <br />

<?php /*
  $baseUrl = dirname($this->baseUrl());
  $this->headScript()
    ->appendFile($baseUrl . '/externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($baseUrl . '/externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($baseUrl . '/externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($baseUrl . '/externals/fancyupload/fancyupload.css');
?>

<h2>
  Install Packages
</h2>



<script type="text/javascript">
  var up;
  var swfPath = '<?php echo $baseUrl . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
  var extraData = {};
  window.addEvent('domready', function() {
    up = new FancyUpload2($('upload-status'), $('upload-list'), {
      verbose: true,
      appendCookieData: true,
      url: $('form-upload').action + '?ul=1',
      path: swfPath,
      typeFilter: {
        'TAR Archive (*.tar)': '*.tar'
      },
      target: 'upload-browse',
      data: extraData,
      onLoad : function() {
        $('upload-status').setStyle('display', '');
        $('upload-fallback').destroy();
        this.target.addEvents({
          click: function() {
            return false;
          },
          mouseenter: function() {
            this.addClass('hover');
          },
          mouseleave: function() {
            this.removeClass('hover');
            this.blur();
          },
          mousedown: function() {
            this.focus();
          }
        });
        $('upload-clear').addEvent('click', function() {
          up.remove(); // remove all files
          return false;
        });
      },
      onSelectFail : function(files) {
        files.each(function(file) {
          new Element('li', {
            'class': 'validation-error',
            html: file.validationErrorMessage || file.validationError,
            title: MooTools.lang.get('FancyUpload', 'removeTitle'),
            events: {
              click: function() {
                this.destroy();
              }
            }
          }).inject(this.list, 'top');
        }, this);
      },
      onComplete : function() {
        // Custom
        //alert('complete!');
      },
      onFileStart : function() {
        // @todo
      },
      onFileRemove : function(file) {
        // @todo
      },
      onSelectSuccess : function() {
        $('upload-list').setStyle('display', '');
        $('upload-status-current').setStyle('display', '');
        $('upload-status-overall').setStyle('display', '');
        up.start();
      },
      onFileSuccess : function(file, response) {
        var json = new Hash(JSON.decode(response, true) || {});
        //if( typeof(console) !== 'undefined' && console != null ) {
          //console.log(response);
          //console.log(json);
        //}
        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          file.info.set('html', '<span>Upload complete.</span>');
        } else {
          file.element.addClass('file-failed');
          file.info.set('html', '<span>' + (json.get('error') ? (json.get('error')) : 'An unknown error has occurred.')) + '</span>';
        }
      },
      onFail : function(error) {
        switch( error ) {
          case 'hidden':
            alert('To enable the embedded uploader, unblock it in your browser and refresh (see Adblock).');
            break;
          case 'blocked':
            alert('To enable the embedded uploader, enable the blocked Flash movie (see Flashblock).');
            break;
          case 'empty':
            alert('A required file was not found, please be patient and we will fix this.');
            break;
          case 'flash':
            alert('To enable the embedded uploader, install the latest Adobe Flash plugin.');
            break;
        }
      }
    });
  });
</script>

<div class="uploader">

  <div id="upload-fallback">
    <form action="<?php echo $this->url() ?>" method="post" id="form-upload">
      <p>
        Description
      </p>
      <input type="file" name="Filedata" />
    </form>
  </div>

  <div id="upload-status" style="display: none;">
    <p>
      Description
    </p>
    <div>
      <a href="javascript:void(0);" id="upload-browse">Upload Packages</a>
      <a href="javascript:void(0);" id="upload-clear" style='display: none;'>Clear List</a>
    </div>
    <div class="upload-status-overall" id="upload-status-overall" style="display:none">
      <div class="overall-title"></div>
      <img src="<?php echo $baseUrl . '/externals/fancyupload/assets/progress-bar/bar.gif' ?>" class="progress overall-progress" />
    </div>
    <div class="upload-status-current" id="upload-status-current" style="display: none">
      <div class="current-title"></div>
      <img src="<?php echo $baseUrl . '/externals/fancyupload/assets/progress-bar/bar.gif' ?>" class="progress current-progress" />
    </div>
    <div class="current-text"></div>
  </div>

  <ul id="upload-list">

  </ul>
</div>
 *
 *
 */ ?>