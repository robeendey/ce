<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PackageSelect.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_View_Helper_PackageSelect extends Zend_View_Helper_Abstract
{
  public function packageSelect(Engine_Package_Manifest_Entity_Package $package)
  {
    $key = $package->getKey();
    $guid = $package->getGuid();
    $safeKey = str_replace('.', '-', $key);
    $safeGuid = str_replace('.', '-', $guid);
    $basename = basename($package->getBasePath());

    ob_start();
    ?>
      <li class="file file-success package_<?php echo $safeGuid ?> package_<?php echo $safeKey ?>">
        <span class="file-name">
          <span class="file-select">
            <input type="checkbox" name="packages[]" id="<?php echo $safeKey ?>" value="<?php echo $package->getKey() ?>" checked="checked"/>
          </span>
          <label for="<?php echo $safeKey ?>">
            <?php echo $package->getKey() ?>
          </label>
        </span>
        <span class="file-info">
          <span class="file-package-info">
            <span class="file-package-info-title">
              <?php if( !$package->getMeta()->getTitle() ): ?>
                <?php echo $guid ?>
              <?php else: ?>
                <?php echo $package->getMeta()->getTitle() ?>
              <?php endif; ?>
              v<?php echo $package->getVersion() ?>
            </span>
            <?php if( $package->getMeta()->getAuthors() ): ?>
              <span class="file-package-info-author">
                by
                <?php echo join(', ', $package->getMeta()->getAuthors()) ?>
              </span>
            <?php endif; ?>
          </span>
        </span>
        <span class="file-package-remove">
          <a href="javascript:void(0);" onclick="removePackage('<?php echo $basename ?>');">
            remove
          </a>
        </span>
      </li>
    <?php
    return ob_get_clean();
  }
}