<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.xml.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>
<?php
$this->navigation()
      ->sitemap()
      ->setContainer($this->navigation)
      ->setFormatOutput(true);
echo $this->navigation()->sitemap();