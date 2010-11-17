<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Default
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7593 2010-10-06 23:59:31Z john $
 * @author     Alex
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'default',
    'version' => '4.0.4',
    'revision' => '$Revision: 7593 $',
    'path' => 'application/themes/default',
    'repository' => 'socialengine.net',
    'meta' => array( // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      'title' => 'Default Theme',
      'thumb' => 'default_theme.jpg',
      'author' => 'Webligo Developments',
      'changeLog' => array(
        '4.0.4' => array(
          'constants.css' => 'Added constant theme_pulldown_contents_list_background_color_active',
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Improved RTL support',
        ),
        '4.0.3' => array(
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Added styles for highlighted text in search',
        ),
        '4.0.2' => array(
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Added styles for delete comment link',
        ),
      ),
    ),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/themes/default',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  ),
) ?>