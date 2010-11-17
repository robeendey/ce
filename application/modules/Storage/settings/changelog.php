<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Storage
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.4' => array(
    'Form/Upload.php' => 'Added missing .jpeg extension to allowed extensions',
    'Plugin/Core.php' => 'Added error suppression to item delete hook',
    'Service/Abstract.php' => 'Fixed issues caused by umask',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.3' => array(
    'Api/Storage/php' => 'Fixed bug with quota handling',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'Api/Storage.php' => 'Typecasting storage quota values',
    'Service/Abstract.php' => 'Silencing notices in chmod',
    'settings/manifest.php' => 'Incremented version',
  ),
  '4.0.1' => array(
    'Api/Storage.php' => 'Storage quotas are now configured by member level',
    'settings/manifest.php' => 'Incremented version',
    'views/scripts/upload/upload.tpl' => 'Fixed IE JS bug',
  ),
) ?>