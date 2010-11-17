<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Fields
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: manifest.php 7562 2010-10-05 22:17:24Z john $
 * @author     John
 */
return array(
  '4.0.5p1' => array(
    'Form/Standard.php' => 'Fixed issue where fields hidden on signup are hidden on edit as well',
    'settings/manifest.php' => 'Incremented version',
    'View/Helper/FieldValueLoop.php' => 'Fixed notice caused by undefined variable',
    'views/scripts/_jsSwitch.tpl' => 'Removed console.log',
  ),
  '4.0.5' => array(
    'Controller/AdminAbstract.php' => 'Fixed rare error caused by missing options',
    'Form/Admin/Field.php' => 'Added ability to hide a field on signup',
    'Form/Element/Country.php' => 'Fixed issue with not adding an empty option when not required',
    'Form/Search.php' => 'Added support for dependent fields; fixed issue where country field was missing an empty option',
    'Form/Standard.php' => 'Added ability to hide a field on signup; fixed issue that allowed a user to bypass fields step on signup',
    'Model/DbTable/Search.php' => 'Added method for memory usage improvements',
    'Model/Meta.php' => 'Required fields now have an empty option, to prevent accidental submission of incorrect options',
    'settings/changelog.php' => 'Added',
    'settings/manifest.php' => 'Incremented version',
    'settings/my-upgrade-4.0.4-4.0.5.sql' => 'Added',
    'settings/my.sql' => 'Incremented version',
    'View/Helper/FieldValueLoop.php' => 'Moved code into separate method for reusage',
    'views/scripts/_jsSwitch.tpl' => 'Fixed bug with dependent fields and radio elements',
  ),
  '4.0.4' => array(
    'Api/Core.php' => 'Fixes memory leak in network admin page',
    'Form/Admin/Field/Currency.php' => 'Causes currencies to be translated based on the language pack in use rather than the browser locale',
    'Form/Element/Currency.php' => 'Fixes rare exceptions caused by locale codes without a territory',
    'Form/Element/Country.php' => 'Fixes incorrect translation of country names',
    'Model/DbTable/Search.php' => 'Added method to delete values for an item',
    'Model/DbTable/Values.php' => 'Added method to delete values for an item',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
    'View/Helper/FieldCountry.php' => 'Fixes incorrect translation of country names',
  ),
  '4.0.3' => array(
    'Model/DbTable/Options.php' => 'ENUM/SET columns in search table get updated when options are added',
    'Model/DbTable/Search.php' => 'Proper handling of search bit',
    'settings/manifest.php' => 'Incremented version',
    'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'Controller/AdminAbstract.php' => 'Fixed problem that would prevent populating form elements for extra configuration options',
    'Form/Search.php' => 'Fixes age search to use minimum age',
    'Form/Standard.php' => 'Missing translation; fixed problem when used as a subform',
    'settings/manifest.php' => 'Increment version',
    'View/Helper/FieldFacebook.php' => 'Fixed improper display of value',
    'views/scripts/_jsAdmin.tpl' => 'Nested dependent fields now display properly',
  ),
  '4.0.1' => array(
    'Controller/AdminAbstract.php' => 'Fixed error caused when trying to link field to parent when it had already been linked; cache is flushed on changing of order',
    'Form/Search.php' => 'Required caused field to be required on search; fixed improper inflection on field types',
    'Model/DbTable/Abstract.php' => 'Added public flushCache method',
    'Model/DbTable/Maps.php' => 'Field is now deleted when last map is removed',
    'Model/DbTable/Search.php' => 'Fixed "typo" that would cause search index to not get removed on field deletion',
    'settings/manifest.php' => 'Incremented version',
    'View/Helper/FieldFacebook.php' => 'Fixed bad rendering of facebook link when given a URL instead of a profile name',
  ),
) ?>