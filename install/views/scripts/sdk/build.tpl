<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: build.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<div class="content sdk" id="content3">


  <h3>Build Packages</h3>

  <p>
    These are the packages we found on your system. Choose the ones you want to
    build into distributable files.
  </p>

  <?php if( $this->status ): ?>

    <div class="tip">
      Your package(s) have been built successfully.
    </div>

  <?php elseif( $this->error ): ?>
  
    <div class="error">
      <?php echo $this->error ?>
    </div>
    
  <?php endif; ?>

  <?php if( empty($this->buildPackages) ): ?>

    <div class="tip">
      No packages were found.
    </div>

  <?php else: ?>

    <form action="<?php echo $this->url() ?>" method="post">
      <table class="sdk_table build">
          <thead>
            <tr>
              <th><input type='checkbox' class='checkbox' onclick="$$('input[type=checkbox]').set('checked', $(this).get('checked'));" /></th>
              <th class="package"><a href="javascript:void(0);">Package</a></th>
              <th class="version"><a href="javascript:void(0);">Version</a></th>
              <th class="type"><a href="javascript:void(0);">Type</a></th>
              <th class="author"><a href="javascript:void(0);">Author</a></th>
              <th class="moreinfo">&nbsp;</th>
            </tr>
          </thead>
          <tbody>

            <?php foreach( $this->buildPackages as $package ): $i = !@$i; ?>
  
              <tr<?php if( !$i ) echo ' class="alt"'; ?>>
                <td>
                  <input type='checkbox' class='checkbox' name="build[]" value="<?php echo $package['key'] ?>" />
                </td>
                <td>
                  <span class="sdk_build_title">
                    <strong><?php echo $package['manifest']['package']['meta']['title'] ?></strong>
                  </span>
                  <div class="sdk_build_moreinfo_container">
                    <div class="sdk_build_location">
                      <i>Location:</i>
                      <p>
                        <?php echo $package['manifest']['package']['path'] ?>
                      </p>
                    </div>
                    <div class="sdk_build_description">
                      <i>Description:</i>
                      <p>
                        <?php echo $package['manifest']['package']['meta']['description'] ?>
                      </p>
                    </div>
                  </div>
                </td>
                <td>
                  <?php echo $package['manifest']['package']['version'] ?>
                </td>
                <td>
                  <?php echo ucfirst($package['manifest']['package']['type']) ?>
                </td>
                <td>
                  <?php echo @$package['manifest']['package']['meta']['author'] ?>
                </td>
                <td class="moreinfo">
                  <a href="javascript:void(0);" onclick="$(this).getParent('tr').getElement('.sdk_build_moreinfo_container').toggleClass('show-more')">
                    More info
                  </a>
                </td>
              </tr>

            <?php endforeach; ?>

          </tbody>
        </table>
        <button type="submit">Build Packages</button>
      </form>

  <?php endif; ?>
</div>