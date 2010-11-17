<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<ul>
  <?php foreach( $this->paginator as $tagmap ):
    $resource = $tagmap->getResource();
    ?>
    <li>
      <div>
        <?php echo $this->htmlLink($resource->getHref(), $this->itemPhoto($resource, 'thumb.normal')) ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>