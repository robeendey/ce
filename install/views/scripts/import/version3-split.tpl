
<script type="text/javascript">
  var token = '<?php echo $this->token ?>';
  var url = '<?php echo $this->url(array('action' => 'version3-remote')) ?>';
  var runOnce = false;
  var state = true;
  var pauseImport = function() {
    state = false;
    $('import_resume').setStyle('display', '');
    $('import_pause').setStyle('display', 'none');
  }
  var resumeImport = function() {
    state = true;
    runOnce = false;
    $('import_resume').setStyle('display', 'none');
    $('import_pause').setStyle('display', '');
    sendImportRequest();
  }
  var toggleLoading = function(state) {
    if( state ) {

    } else {
      
    }
  }
  var sendImportRequest = function() {
    if( runOnce || !state ) {
      return;
    }
    runOnce = true;
    toggleLoading(true);
    
    (new Request.JSON({
      url : url,
      data : {
        token : token
      },
      onComplete : function(responseJSON, responseText) {
        runOnce = false;
        toggleLoading(false);
        
        // An error occurred
        if( $type(responseJSON) != 'object' ) {
          $('import_fatal_error').set('html', 'ERROR: ' + responseText);
          pauseImport();
          return;
        }
        if( !$type(responseJSON.status) || !responseJSON.status ) {
          if( $type(responseJSON.error) ) {
            $('import_fatal_error').set('html', 'ERROR: ' + responseJSON.error);
          } else {
            $('import_fatal_error').set('html', 'ERROR: ' + responseText);
          }
          pauseImport();
          return;
        }

        // Normal

        // Special case for done
        if( $type(responseJSON.complete) ) {
          responseJSON.migratorCurrent = responseJSON.migratorTotal;
          responseJSON.totalProcessed = responseJSON.totalRecords;
          responseJSON.timeRemainingStr = '0 hours, 0 minutes, 0 seconds (0 seconds total)';
          responseJSON.ratioComplete = 1;
        }

        // Progress
        var progressString = '';

        // Show step progress
        if( $type(responseJSON.migratorCurrent) ) {
          progressString += responseJSON.migratorCurrent + ' of ' + responseJSON.migratorTotal + ' steps have been completed. ';
        }

        // Show record progress
        if( $type(responseJSON.totalRecords) && $type(responseJSON.totalProcessed) ) {
          if( progressString != '' ) {
            progressString += '<br />' + "\n";
          }
          progressString += responseJSON.totalProcessed + ' of ' + responseJSON.totalRecords + ' records have been completed. ';
        }

        // Show time spent
        if( $type(responseJSON.deltaTimeStr) && responseJSON.deltaTimeStr != '' ) {
          if( progressString != '' ) {
            progressString += '<br />' + "\n";
          }
          progressString += ' ' + responseJSON.deltaTimeStr + ' have passed.';
        }

        // Show time remaining
        if( $type(responseJSON.timeRemainingStr) && responseJSON.timeRemainingStr != '' ) {
          if( progressString != '' ) {
            progressString += '<br />' + "\n";
          }
          progressString += ' ' + responseJSON.timeRemainingStr + ' remaining.';
        }

        // Show percent progress
        if( $type(responseJSON.ratioComplete) ) {
          if( progressString != '' ) {
            progressString += '<br />' + "\n";
          }
          progressString += ' ' + (Math.round(parseFloat(responseJSON.ratioComplete) * 1000) / 10) + ' percent complete.';
        }
        
        if( '' != progressString ) {
          $('import_progress').set('html', progressString);
        }

        // Done!
        if( $type(responseJSON.complete) ) {
          (new Element('li', {
            'html' : '<h3>' + 'Complete!' + '</h3>' + '<ul class="import_log"><li class="notice">' + 'The migration is complete!' + '</li></ul>'
          })).inject($('import_log_container').getElement('.import_log_section'), 'top');
        }

        else {
          // Check for progress report
          var className = responseJSON.className;
          var elementIdentity = 'import_log_' + className;
          var element = $(elementIdentity);
          if( !element ) {
            var tmpEl = new Element('li');
            tmpEl.inject($('import_log_container').getElement('ul'), 'top');
            (new Element('h3', {
              'html' : className
            })).inject(tmpEl);
            element = new Element('ul', {
              'id' : elementIdentity,
              'class' : 'import_log'
            });
            element.inject(tmpEl);
          }
          element.empty();

          $A(responseJSON.messages).each(function(message) {
            (new Element('li', {
              'class' : ( message.toLowerCase().indexOf('error') != -1 ? 'error' : ( message.toLowerCase().indexOf('warning') != -1 ? 'warning' : 'notice' ) ),
              'html' : message
            })).inject(element);
          });

          if( state ) {
            sendImportRequest();
          }
        }
      }
    })).send();
  }
  window.addEvent('load', function() {
    resumeImport();
  });

</script>

<div>
  <a href="javascript:void(0);" onclick="resumeImport();" id="import_resume" style="display:none;">
    Resume import
  </a>
  <a href="javascript:void(0);" onclick="pauseImport();" id="import_pause" style="display:none;">
    Pause Import
  </a>
  <div>
    Token: <?php echo $this->token ?>
    <br />
    URI: http://<?php echo $_SERVER['HTTP_HOST'] ?><?php echo $this->url(array(), 'default', true) ?>
  </div>
</div>
<br />

<div id="import_fatal_error">

</div>
<br />

<div id="import_progress">
  
</div>
<br />

<div id="import_log_container">
  <ul class="import_log_section">
    
  </ul>
</div>