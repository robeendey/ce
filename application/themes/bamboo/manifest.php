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
    'name' => 'bamboo',
    'version' => '4.0.1',
    'revision' => '$Revision: 7593 $',
    'path' => 'application/themes/bamboo',
    'repository' => 'socialengine.net',
    'meta' => array( // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      'title' => 'Bamboo Theme',
      'thumb' => 'bamboo_theme.jpg',
      'author' => 'Webligo Developments',
      'changeLog' => array(
        '4.0.1' => array(
          'manifest.php' => 'Incremented version',
          'theme.css' => 'Uses fixed relative URL support in Scaffold',
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
      'application/themes/bamboo',
    ),
  ),
  'files' => array(
    'theme.css',
    'constants.css',
  )
) ?>