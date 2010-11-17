<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     Jung
 */
return array(
  '4.0.5' => array(
    'Api/Core.php' => 'Added support for filtering by having photo',
    'controllers/IndexController.php' => 'Added registered privacy type',
    'externals/images/nophoto_classified_thumb_icon.png' => 'Added',
    'externals/styles/main.css' => 'Added styles for browse page',
    'Form/Admin/Settings/Level.php' => 'Added registered privacy type',
    'Form/Create.php' => 'Added registered privacy type',
    'Form/Search.php' => 'Improved field search functions',
    'Model/Classified.php' => 'Changed search indexing columns',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added idle support; added registered privacy type',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/index/create.tpl' => 'Added support for dependent fields',
    'views/scripts/index/edit.tpl' => 'Added support for dependent fields',
    'views/scripts/index/index.tpl' => 'Improved field search functions; added support for dependent fields',
  ),
  '4.0.4' => array(
    'controllers/IndexController.php' => 'Added localization to the archive list; menus now use the menu system',
    'externals/styles/main.css' => 'Improved RTL support; style tweak',
    'Form/Create.php' => 'Removing deprecated code',
    'Form/Edit.php' => 'Removing deprecated code',
    'Model/Classified.php' => 'Adding slug to view url',
    'Plugin/Menus.php' => 'Added; menus now use the menu system',
    'Plugin/Task/Maintenance/RebuildPrivacy.php' => 'Added to fix privacy issues in the feed',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.3-4.0.4.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'views/scripts/_FancyUpload.tpl' => 'Added missing translation',
    'views/scripts/_formButtonCancel.tpl' => 'Removing deprecated code',
    'views/scripts/admin-settings/delete.tpl' => 'Fixed incorrect route',
    'views/scripts/index/delete.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/edit.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/index.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/manage.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/list.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/success.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'views/scripts/index/view.tpl' => 'Menus now use the menu system; removing deprecated routes',
    'widgets/profile-classifieds/index.tpl' => 'Menus now use the menu system; removing deprecated routes',
    '/application/languages/en/classified.csv' => 'Added phrases',
  ),
  '4.0.3' => array(
    'Api/Core.php' => 'Fixed bug in filtering by fields',
    'controllers/AdminFieldsController.php' => 'Fixed missing elements in edit field form',
    'controllers/IndexController.php' => 'Fixed bug in filtering by fields; fixed activity privacy bug; added correct locale date format to archive list',
    'Form/Create.php' => 'Fixed handling on auth elements',
    'Model/Classified.php' => 'Fixed bug where classifieds would not show up in search',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.2-4.0.3.sql' => 'Added',
    'settings/my.sql' => 'Permissions tweaks; incremented version',
    'views/scripts/admin-manage/index.tpl' => 'Added missing translation; added correct date format',
    'views/scripts/index/create.tpl' => 'Fix for unlimited quotas',
    'views/scripts/index/edit.tpl' => 'Fixed bug in fields; added missing translation',
    'views/scripts/index/manage.tpl' => 'Fix for unlimited quotas',
    '/application/languages/en/classified.csv' => 'Added phrases',
  ),
  '4.0.2' => array(
    'controllers/AdminLevelController.php' => 'Various level settings fixes and enhancements',
    'controllers/IndexController.php' => 'Fixed problem preventing saving of fields',
    'Form/Create.php' => 'Fixed problem preventing saving of fields',
    'Form/Admin/Level.php' => 'Moved',
    'Form/Admin/Settings/Level.php' => 'Various level settings fixes and enhancements',
    'Form/Custom/Fields.php' => 'Fixed problem preventing saving of fields',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.1-4.0.2.sql' => 'Added',
    'settings/my.sql' => 'Various level settings fixes and enhancements',
    'views/scripts/index/view.tpl' => 'Added nl2br on body',
  ),
  '4.0.1' => array(
    'Api/Core.php' => 'Better cleanup of temporary files; adjustment for trial',
    'controllers/AdminLevelController.php' => 'Fixed problem in level select',
    'controllers/IndexController.php' => 'Fixed bug with public viewing classifieds',
    'Form/Custom/Fields.php' => 'Adjustment for trial',
    'Model/Classified.php' => 'Better cleanup of temporary files',
    'Plugin/Core.php' => 'Query optimization',
    'settings/manifest.php' => 'Incremented version',
    'views/scripts/admin-level/index.tpl' => 'Fixed problem in level select',
    'views/scripts/admin-settings/delete.tpl' => 'Fixed typo',
  ),
) ?>