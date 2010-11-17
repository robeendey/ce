<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: select.tpl 7533 2010-10-02 09:42:49Z john $
 * @author     John
 */
?>

<?php
  $baseUrl = rtrim(str_replace('\\', '/', dirname($this->baseUrl())), '/');
  $this->headScript()
    ->appendFile($baseUrl . '/externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($baseUrl . '/externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($baseUrl . '/externals/fancyupload/FancyUpload2.js');
  $this->headLink()
    ->appendStylesheet($baseUrl . '/externals/fancyupload/fancyupload.css');
?>

<script type="text/javascript">
  var up;
  var swfPath = '<?php echo $baseUrl . '/externals/fancyupload/Swiff.Uploader.swf' ?>';
  var extraData = {
    format: 'json'
  };
  var currentlyUploading = false;
  var currentlyExtracting = false;
  var pendingExtraction = [];
  <?php if( !empty($this->toExtractPackages) ): ?>
    pendingExtraction = <?php echo Zend_Json::encode($this->toExtractPackages) ?>;
  <?php endif; ?>

  window.addEvent('click', function(event) {
    var element = $(event.target);
    if( element.get('tag') == 'input' && element.get('type') == 'checkbox' ) {
      checkCanContinue();
    }
  });
  
  var checkCanContinue = function()
  {
    // Check for selection
    var hasChecked = false;
    $$('input[type=checkbox]').each(function(el) {
      if( el.checked ) {
        hasChecked = true;
      }
    });
    // Check for pending extraction
    checkPendingExtraction();

    // Do the message stuff
    if( currentlyUploading || currentlyExtracting || pendingExtraction.length > 0 ) {
      $('package_select_continue').set('class', 'package_select_error_uploading');
    } else if( !hasChecked ) {
      $('package_select_continue').set('class', 'package_select_error_noselection');
    } else {
      $('package_select_continue').set('class', 'package_select_okay');
    }
  }
  
  var checkPendingExtraction = function() {
    if( currentlyExtracting || pendingExtraction.length == 0 ) {
      return;
    }

    currentlyExtracting = true;

    // Start extracting
    var url = '<?php echo $this->url(array('action' => 'extract')) ?>';
    var extractPackage, uploadEl;
    if( $type(pendingExtraction[0]) == 'string' ) {
      extractPackage = pendingExtraction[0];
      uploadEl = $$('.package_' + extractPackage.replace(/\./g, '_'))[0];
    } else if( $type(pendingExtraction[0]) == 'object' ) {
      extractPackage = pendingExtraction[0].info;
      uploadEl = pendingExtraction[0].el;
    } else {
      alert('Whoops wrong data type: ' + $type(pendingExtraction[0]));
    }
    if( !uploadEl ) {
      alert('Missing upload element!');
      throw 'Missing upload element!';
    }

    // Update message

    var uploadMessageEl = uploadEl.getElement('.file-message');
    if( !uploadMessageEl ) {
      uploadMessageEl = new Element('span', {
        'class' : 'file-message'
      });
      uploadMessageEl.inject(uploadEl.getElement('.file-info') || uploadEl);
    }
    uploadMessageEl.set('html', 'Extracting ...').addClass('file-loading');
    //$('upload-image').clone().set('id', 'extracting-image').inject(uploadEl);
    
    var request = new Request.JSON({
      url: url,
      data : {
        'package' : extractPackage,
        'format' : 'json'
      },
      onComplete : function(responseJSON, responseText) {
        // Remove current package regardless
        pendingExtraction.shift(); //erase(extractPackage);
        currentlyExtracting = false;

        uploadMessageEl.removeClass('file-loading');

        // Bad response
        if( !$type(responseJSON) ) {
          uploadMessageEl.set('html', 'Extract error: Bad response');

        // Error
        } else if( $type(responseJSON.error) ) {
          uploadMessageEl.set('html', 'Extract error: ' + responseJSON.error);

        // Okay
        } else if( $type(responseJSON.status) ) {
          replacePackageListItems(responseJSON.packagesInfo, uploadEl);

        // Wth
        } else {
          uploadMessageEl.set('html', 'Unknown extract error: ' + responseText);
        }

        // Check for more extraction
        checkCanContinue();
      }
    });
    request.send();
  }

  var replacePackageListItems = function(packages, element) {
    $A(packages).each(function(info) {
      var guid = info.data.type + '-' + info.data.name;
      var key = info.key;
      $$('.package_' + guid).destroy();
      $$('.package_' + key).destroy();
      
      Elements.from(info.html).inject(element, 'after');
    });
    element.destroy();
  }

  var removePackage = function(packageKey) {
    var url = '<?php echo $this->url(array('action' => 'select-delete')) ?>';
    var request = new Request.JSON({
      url : url,
      data : {
        format : 'json',
        'package' : packageKey
      },
      onComplete : function(responseJSON) {
        if( $type(responseJSON) && $type(responseJSON.error) ) {
          alert('An error has occurred: ' + responseJSON.error);
        } else if( !$type(responseJSON) || !$type(responseJSON.status) || !responseJSON.status ) {
          alert('An unknown error has occurred.');
        } else {
          //success
          $$('.package_' + packageKey.replace(/\./g, '-')).destroy();
          checkCanContinue();
        }
      }
    });
    request.send();
  }
  
  window.addEvent('domready', function() {

    checkCanContinue();

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
      timeLimit: 600,
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
      onSelectSuccess : function(successFiles) {
        $('upload-list').setStyle('display', '');
        $('upload-status-current').setStyle('display', '');
        $('upload-status-overall').setStyle('display', '');
        up.start();
        currentlyUploading = true;
        checkCanContinue();
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
        currentlyUploading = false;
        checkCanContinue();
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
      },
      onFileStart : function(file) {
        //$('upload-image').inject(file.element);
        file.info.set('html', '<span class="file-message file-loading">Uploading...</span>');
      },
      onFileRemove : function(file) {
        // @todo
      },
      onFileSuccess : function(file, response) {
        //$('upload-image').inject($('upload-status'));
        var json = new Hash(JSON.decode(response, true) || {});
        if (json.get('status') == '1') {
          file.element.addClass('file-success');
          file.info.set('html', '<span class="file-message">Upload successful! Pending extraction.</span>');
          pendingExtraction.push({
            'info' : json['file'],
            'el'   : file.element
          });
          checkCanContinue();
        } else {
          file.element.addClass('file-failed');
          file.info.set('html', '<span class="file-message">' + (json.get('error') ? (json.get('error')) : 'An unknown error has occurred.')) + '</span>';
        }
        
        // Move to bottom
        //file.element.inject(file.element.getParent());
      }
    });
  });
</script>

<h3>
  Install Packages
</h3>

<?php
  // Navigation
  echo $this->render('_installMenu.tpl')
?>

<br />

<p>
  Let's get started with installing your new packages. First, you will need to upload
  the packages you want to install. Click the "Upload Packages" link below to select one or
  multiple packages from your computer to upload them to the server.
  <br />
  Note: The packages are extracted on upload, so the progress bar will pause at 100% for
  up to several minutes (depending on the size of the package).
</p>

<br />

<div class="package_uploader">
  <div class="package_uploader_main">
    <div class="upload-fallback" id="upload-fallback">
      <form action="<?php echo $this->url(array('action' => 'upload')) ?>" method="post" id="form-upload" enctype="multipart/form-data">
        <input type="file" name="Filedata" />
        <input type="hidden" name="ul" value="1" />
        <button type="submit">Submit</button>
      </form>
    </div>
    <div id="upload-status" style="display: none;">
      <div class="upload-buttons" id="upload-buttons">
        <a href="javascript:void(0);" id="upload-browse" class="buttonlink package_uploader_choosepackages"><?php echo $this->translate('Add Packages') ?></a>
        <a href="javascript:void(0);" id="upload-clear" style='display: none;'><?php echo $this->translate('Clear List') ?></a>
        <!--
        <a href="javascript:void(0);" id="select-check-all" onclick="$$('input[type=checkbox]').set('checked', true);">Check All</a>
        -->
      </div>
      <div class="upload-status-overall" id="upload-status-overall" style="display:none">
        <div class="overall-title"></div>
        <img class="progress overall-progress" alt="" src="<?php echo $baseUrl . '/externals/fancyupload/assets/progress-bar/bar.gif' ?>" />
      </div>
      <div class="upload-status-current" id="upload-status-current" style="display: none">
        <div class="current-title"></div>
        <img class="progress current-progress" alt="" src="<?php echo $baseUrl . '/externals/fancyupload/assets/progress-bar/bar.gif' ?>" />
      </div>
      <div class="current-text"></div>
      <span class="upload-image" id="upload-image">
        <img src="<?php echo $this->baseUrl() ?>/externals/images/loading.gif" alt="Uploading ..." />
      </span>
    </div>
  </div>
</div>



<form action="<?php echo $this->url(array('action' => 'prepare')) ?>" method="post">
  <ul class="upload-list selected-packages-list" id="upload-list">
  </ul>
  <ul class="selected-packages-list extracted-packages-list">
    <?php foreach( (array) $this->toExtractPackages as $toExtractPackage ): ?>
      <?php echo $this->packageSelectSimple($toExtractPackage) ?>
    <?php endforeach; ?>
    <?php foreach( $this->extractedPackages as $package ): ?>
      <?php echo $this->packageSelect($package) ?>
    <?php endforeach; ?>
  </ul>

  <br />

  <div id="package_select_continue">

    <div class="package_select_uploading_message">
      Please wait until the upload finishes or while archives are extracted.
    </div>
    
    <div class="package_select_noselection_message">
      Please upload or select a package.
    </div>
    
    <div class="package_select_continue_message">
      <p>
        If you're ready to install the packages checked above, click the button below.
        In the next step, we will check to make sure your server has everything it needs
        to complete the installation.
      </p>

      <br />
      
      <div>
        <button type="submit">Continue</button>
        or <a href="./">cancel installation</a>
      </div>
    </div>
    
  </div>
  
</form>