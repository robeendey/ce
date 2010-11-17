<?php
/**
 * SocialEngine
 *
 * @category   Application_Theme
 * @package    Bamboo
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7593 2010-10-06 23:59:31Z john $
 * @author     Bryan
 */

return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'snowbot',
    'version' => '4.0.0',
    'revision' => '$Revision: 7593 $',
    'path' => 'application/themes/snowbot',
    'repository' => 'socialengine.net',
    'meta' => array( // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      'title' => 'Snowbot Theme',
      'thumb' => 'snowbot_theme.jpg',
      'author' => 'Webligo Developments'
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
      'application/themes/snowbot',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>