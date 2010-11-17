<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manage.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div class="content sdk manage" id="content4">
  
  <div class="tip" id="package-created"><span>Package(s) successfully created. Download below.</span></div>
  

  <h3>Manage Package Files</h3>

  <p>
    These are the built package files we found on your system at
    <i>/application/temporary/package/sdk</i>
  </p>

  <?php if( empty($this->packages) ): ?>

    <div class="tip">
      No packages were found.
    </div>
  
  <?php else: ?>

    <div class="button-container">
      <button onclick="$('sdk_manage_form').set('action', '<?php echo $this->url(array('action' => 'combine')) ?>').submit();">Combine</button>
      <button onclick="$('sdk_manage_form').set('action', '<?php echo $this->url(array('action' => 'delete')) ?>').submit();">Delete</button>
    </div>

    <form action="<?php echo $this->url() ?>" method="get" id="sdk_manage_form">
    
      <table class="sdk_table manage">
        <thead>
          <tr>
            <th><input type='checkbox' class='checkbox' onclick="$$('input[type=checkbox]').set('checked', $(this).get('checked'));" /></th>
            <th class="package-file"><a href="javascript:void(0);">Package File</a></th>
            <th class="package-date"><a href="javascript:void(0);">Date Built</a></th>
          </tr>
        </thead>
        <tbody>

          <?php foreach( $this->packages as $index => $package ): ?>
            <tr>
              <td>
                <input type='checkbox' class='checkbox' name="actions[]" value="<?php echo basename($this->packageFiles[$index]) ?>">
              </td>
              <td>
                <a href="<?php echo $this->url(array('action' => 'download')) ?>?file=<?php echo urlencode(basename($this->packageFiles[$index])) ?>" class="buttonlink sdk-download">
                  <?php echo basename($this->packageFiles[$index]) ?>
                </a>
              </td>
              <td>
                <?php echo $package ? $package->getMeta()->getDate() : '' ?>
              </td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>

    </form>

  <?php endif; ?>
</div>