/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: my-upgrade-4.0.0beta3-4.0.0rc1.sql 7244 2010-09-01 01:49:53Z john $
 * @author     Steve
 */
ALTER TABLE  `engine4_album_albums` ADD  `category_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0'
