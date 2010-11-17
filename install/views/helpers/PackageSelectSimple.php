<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: PackageSelectSimple.php 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Install
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Install_View_Helper_PackageSelectSimple extends Zend_View_Helper_Abstract
{
  public function packageSelectSimple($packageKey)
  {
    ob_start();
    ?>
      <li class="file file-success package_<?php echo str_replace('.', '_', $packageKey) ?>">
        <span class="file-name">
          <?php echo $packageKey ?>
        </span>
        <span class="file-info">
          <span class="file-message">
            Pending extraction.
          </span>
        </span>
      </li>
    <?php
    return ob_get_clean();
  }
}