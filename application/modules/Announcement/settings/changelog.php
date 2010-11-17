<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Announcement
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.3' => array(
    'Model/Announcement.php' => 'Removed redundant code',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    '/application/languages/en/announcement.csv' => 'Added phrases',
  ),
  '4.0.1' => array(
    'settings/manifest.php' => 'Incremented version',
    'widgets/list-announcements/index.tpl' => 'Switched array to paginator',
  ),
) ?>